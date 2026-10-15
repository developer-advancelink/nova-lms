{{-- SHARED INBOX PARTIAL — Teams-style compose with edit/delete --}}
@php
    $replyAction = Auth::user()->role === 'lecturer' ? '/lecturer/inbox/reply' : '/inbox/reply';
    $msgBase     = Auth::user()->role === 'lecturer' ? '/lecturer/inbox/messages' : '/inbox/messages';
    $recipientsList = json_encode(($recipients ?? collect())->map(fn($r) => [
        'id'   => $r->id,
        'name' => $r->name,
        'role' => ucfirst($r->role),
    ])->values());
@endphp

@php
    $threadsJson = json_encode($threads->map(function ($thread) {
        $other = $thread->sender_id === Auth::id() ? $thread->receiver : $thread->sender;
        $latestReply = $thread->replies->last() ?? $thread;
        return [
            'id'          => $thread->id,
            'otherName'   => $other->name,
            'subject'     => $thread->subject,
            'preview'     => Str::limit($latestReply->body, 50),
            'courseCode'  => $thread->course?->code,
            'timeAgo'     => $thread->created_at->diffForHumans(null, true),
            'isUnread'    => is_null($thread->read_at) && $thread->receiver_id === Auth::id(),
            'isSent'      => $thread->sender_id === Auth::id(),
            'isActive'    => request('thread') == $thread->id,
        ];
    })->values());
@endphp
<div x-data="{
    mode: '{{ request()->has('thread') ? 'thread' : 'empty' }}',
    filter: 'all',
    threads: {{ $threadsJson }},
    get filteredThreads() {
        if (this.filter === 'unread') return this.threads.filter(t => t.isUnread);
        if (this.filter === 'sent')   return this.threads.filter(t => t.isSent);
        return this.threads;
    },
    recipientSearch: '',
    recipientId: null,
    recipientName: '',
    showDropdown: false,
    allRecipients: {{ $recipientsList }},
    editingId: null,
    editBody: '',
    deletingId: null,
    get filteredRecipients() {
        if (!this.recipientSearch) return this.allRecipients;
        const q = this.recipientSearch.toLowerCase();
        return this.allRecipients.filter(r => r.name.toLowerCase().includes(q));
    },
    selectRecipient(r) {
        this.recipientId = r.id;
        this.recipientName = r.name;
        this.recipientSearch = r.name;
        this.showDropdown = false;
    },
    openCompose() {
        this.mode = 'compose';
        this.recipientSearch = '';
        this.recipientId = null;
        this.recipientName = '';
        this.showDropdown = false;
    },
    closeCompose() {
        this.mode = '{{ request()->has('thread') ? 'thread' : 'empty' }}';
    },
    startEdit(id, body) {
        this.editingId = id;
        this.editBody = body;
        this.deletingId = null;
    },
    cancelEdit() {
        this.editingId = null;
        this.editBody = '';
    },
    async saveEdit(id, csrfToken, msgBase) {
        const res = await fetch(msgBase + '/' + id, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
            },
            body: JSON.stringify({ body: this.editBody }),
        });
        if (res.ok) {
            document.getElementById('msg-body-' + id).textContent = this.editBody;
            this.cancelEdit();
        }
    },
    confirmDelete(id) {
        this.deletingId = id;
        this.editingId = null;
    },
    cancelDelete() {
        this.deletingId = null;
    },
    async doDelete(id, csrfToken, msgBase) {
        const res = await fetch(msgBase + '/' + id, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': csrfToken },
        });
        if (res.ok) {
            const el = document.getElementById('msg-' + id);
            if (el) el.remove();
            this.deletingId = null;
        }
    }
}" class="flex rounded-2xl bg-white shadow-sm overflow-hidden" style="min-height: 75vh;">

    {{-- LEFT: CONVERSATION LIST --}}
    <div class="w-80 border-r border-[#C8D3E0] flex-shrink-0 flex flex-col">

        <div class="flex items-center justify-between px-4 py-4 border-b border-[#C8D3E0]">
            <span class="text-sm font-semibold text-slate-900">Inbox</span>
            <button @click="openCompose()"
                    class="rounded-lg bg-[#1B3A5C] text-white text-xs font-medium px-3 py-1.5 hover:bg-[#24507A] transition-colors">
                + Compose
            </button>
        </div>

        <div class="flex border-b border-[#C8D3E0] text-xs">
            <button @click="filter = 'all'"
                    :class="filter === 'all' ? 'font-medium text-[#1B3A5C] bg-[#E8EEF4]' : 'text-slate-500 hover:bg-[#E8EEF4]'"
                    class="flex-1 py-2.5 border-r border-[#C8D3E0] transition-colors">All</button>
            <button @click="filter = 'unread'"
                    :class="filter === 'unread' ? 'font-medium text-[#1B3A5C] bg-[#E8EEF4]' : 'text-slate-500 hover:bg-[#E8EEF4]'"
                    class="flex-1 py-2.5 border-r border-[#C8D3E0] transition-colors">
                Unread
                <span x-show="threads.filter(t => t.isUnread).length > 0"
                      x-text="threads.filter(t => t.isUnread).length"
                      class="ml-1 inline-flex items-center justify-center w-4 h-4 rounded-full bg-[#1B3A5C] text-white text-[9px] font-bold"></span>
            </button>
            <button @click="filter = 'sent'"
                    :class="filter === 'sent' ? 'font-medium text-[#1B3A5C] bg-[#E8EEF4]' : 'text-slate-500 hover:bg-[#E8EEF4]'"
                    class="flex-1 py-2.5 transition-colors">Sent</button>
        </div>

        <ul class="flex-1 overflow-y-auto divide-y divide-[#C8D3E0]">

            <template x-if="filteredThreads.length === 0">
                <li class="px-4 py-10 text-sm text-slate-400 text-center">
                    <span x-show="filter === 'unread'">No unread messages.</span>
                    <span x-show="filter === 'sent'">No sent messages.</span>
                    <span x-show="filter === 'all'">No messages yet.</span>
                </li>
            </template>

            <template x-for="thread in filteredThreads" :key="thread.id">
                <li :class="thread.isActive
                        ? 'bg-[#E8EEF4]'
                        : thread.isUnread
                            ? 'bg-[#F2F5F9] hover:bg-[#E8EEF4]'
                            : 'hover:bg-[#F2F5F9]'"
                    class="cursor-pointer transition-colors"
                    @click="window.location = '?thread=' + thread.id">

                    <div class="px-4 py-3.5">
                        <div class="flex justify-between items-start mb-1">
                            <span class="text-sm"
                                  :class="thread.isUnread ? 'font-semibold text-slate-900' : 'font-medium text-slate-700'"
                                  x-text="thread.otherName"></span>
                            <span class="text-xs text-slate-400 flex-shrink-0 ml-2" x-text="thread.timeAgo"></span>
                        </div>
                        <p class="text-xs truncate mb-1"
                           :class="thread.isUnread ? 'font-semibold text-slate-800' : 'text-slate-600'"
                           x-text="thread.subject"></p>
                        <p class="text-xs text-slate-400 truncate" x-text="thread.preview"></p>
                        <div class="flex items-center justify-between mt-2">
                            <span x-show="thread.courseCode"
                                  x-text="thread.courseCode"
                                  class="text-xs font-medium text-[#1B3A5C] bg-[#E8EEF4] border border-[#C8D3E0] rounded-full px-2.5 py-0.5"></span>
                            <span x-show="thread.isUnread"
                                  class="w-2 h-2 rounded-full bg-[#1B3A5C] ml-auto"></span>
                        </div>
                    </div>

                </li>
            </template>

        </ul>

    </div>

    {{-- RIGHT PANEL --}}
    <div class="flex-1 flex flex-col min-w-0">

        {{-- COMPOSE MODE --}}
        <div x-show="mode === 'compose'" x-cloak style="display:none" class="flex flex-col h-full">

            <div class="px-6 py-4 border-b border-[#C8D3E0] flex items-center justify-between flex-shrink-0">
                <h2 class="text-base font-semibold text-slate-900">New Message</h2>
                <button @click="closeCompose()"
                        class="rounded-lg p-1.5 text-slate-400 hover:bg-[#E8EEF4] hover:text-slate-700 transition-colors">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form method="POST" action="{{ $replyAction }}" class="flex flex-col flex-1 min-h-0">
                @csrf
                <input type="hidden" name="receiver_id" :value="recipientId">

                {{-- TO --}}
                <div class="flex items-start gap-3 px-6 py-3 border-b border-[#E8EEF4] relative" @click.away="showDropdown = false">
                    <span class="text-xs font-semibold text-slate-400 pt-2.5 flex-shrink-0">To:</span>
                    <div class="flex-1 relative">
                        <input type="text" x-model="recipientSearch"
                               @focus="showDropdown = true"
                               @input="showDropdown = true; recipientId = null"
                               placeholder="Search name..."
                               autocomplete="off"
                               class="w-full bg-transparent text-sm text-slate-900 placeholder:text-slate-400
                                      outline-none py-2 border-b border-transparent focus:border-[#1B3A5C] transition-colors">
                        <div x-show="recipientId" x-cloak style="display:none" class="absolute inset-0 flex items-center">
                            <span class="inline-flex items-center gap-1.5 bg-[#1B3A5C] text-white text-xs font-medium rounded-full px-3 py-1">
                                <span x-text="recipientName"></span>
                                <button type="button" @click="recipientId = null; recipientName = ''; recipientSearch = ''; showDropdown = false"
                                        class="hover:text-[#C9A227] transition-colors leading-none">×</button>
                            </span>
                        </div>
                        <div x-show="showDropdown && filteredRecipients.length > 0 && !recipientId"
                             x-cloak style="display:none"
                             class="absolute top-full left-0 right-0 mt-1 bg-white border border-[#C8D3E0] rounded-xl shadow-lg z-10 overflow-hidden max-h-48 overflow-y-auto">
                            <template x-for="r in filteredRecipients" :key="r.id">
                                <button type="button" @click="selectRecipient(r)"
                                        class="w-full flex items-center gap-3 px-4 py-2.5 hover:bg-[#E8EEF4] transition-colors text-left">
                                    <div class="w-7 h-7 rounded-lg bg-[#C9A227] text-[#0F1B2D] flex items-center justify-center text-[10px] font-bold flex-shrink-0"
                                         x-text="r.name.split(' ').map(w => w[0]).slice(0,2).join('').toUpperCase()"></div>
                                    <div>
                                        <p class="text-sm font-medium text-slate-900" x-text="r.name"></p>
                                        <p class="text-xs text-slate-400" x-text="r.role"></p>
                                    </div>
                                </button>
                            </template>
                        </div>
                        <div x-show="showDropdown && filteredRecipients.length === 0 && recipientSearch && !recipientId"
                             x-cloak style="display:none"
                             class="absolute top-full left-0 right-0 mt-1 bg-white border border-[#C8D3E0] rounded-xl shadow-lg z-10 px-4 py-3 text-xs text-slate-400">
                            No results for "<span x-text="recipientSearch"></span>"
                        </div>
                    </div>
                </div>

                {{-- SUBJECT --}}
                <div class="flex items-center gap-3 px-6 py-3 border-b border-[#E8EEF4]">
                    <span class="text-xs font-semibold text-slate-400 flex-shrink-0">Subject:</span>
                    <input type="text" name="subject" required placeholder="Message subject..."
                           class="flex-1 bg-transparent text-sm text-slate-900 placeholder:text-slate-400
                                  outline-none py-2 border-b border-transparent focus:border-[#1B3A5C] transition-colors">
                </div>

                {{-- COURSE --}}
                <div class="flex items-center gap-3 px-6 py-3 border-b border-[#E8EEF4]">
                    <span class="text-xs font-semibold text-slate-400 flex-shrink-0">Course:</span>
                    <select name="course_id"
                            class="flex-1 bg-transparent text-sm text-slate-600 outline-none py-2
                                   border-b border-transparent focus:border-[#1B3A5C] transition-colors">
                        <option value="">No course (optional)</option>
                        @foreach($inboxCourses ?? [] as $course)
                            <option value="{{ $course->id }}">{{ $course->code }} — {{ $course->name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- BODY --}}
                <div class="flex-1 flex flex-col min-h-0 px-6 py-4">
                    <textarea name="body" required placeholder="Type your message here..."
                              class="flex-1 w-full bg-transparent text-sm text-slate-900 placeholder:text-slate-400
                                     outline-none resize-none leading-relaxed"></textarea>
                </div>

                {{-- SEND BAR --}}
                <div class="border-t border-[#C8D3E0] px-6 py-3 flex justify-between items-center flex-shrink-0 bg-[#F2F5F9]">
                    <button type="button" @click="closeCompose()"
                            class="text-xs font-medium text-slate-500 hover:text-slate-700 transition-colors">
                        Discard
                    </button>
                    <button type="submit"
                            :disabled="!recipientId"
                            :class="recipientId ? 'bg-[#1B3A5C] hover:bg-[#24507A]' : 'bg-slate-300 cursor-not-allowed'"
                            class="rounded-lg text-white text-sm font-medium px-5 py-2 transition-colors flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                        </svg>
                        Send
                    </button>
                </div>
            </form>

        </div>

        {{-- THREAD MODE --}}
        <div x-show="mode === 'thread'" x-cloak style="display:none" class="flex flex-col h-full">

            @if($activeThread)

                <div class="px-6 py-4 border-b border-[#C8D3E0] flex items-start justify-between flex-shrink-0">
                    <div>
                        <h2 class="text-base font-semibold text-slate-900 mb-1">{{ $activeThread->subject }}</h2>
                        @if($activeThread->course)
                            <p class="text-xs text-slate-400">{{ $activeThread->course->code }} — {{ $activeThread->course->name }}</p>
                        @endif
                    </div>
                    <a href="?" class="text-xs font-medium text-slate-500 border border-[#C8D3E0] rounded-lg px-3 py-1.5 hover:bg-[#E8EEF4] transition-colors flex-shrink-0 ml-4">
                        ← Back
                    </a>
                </div>

                {{-- MESSAGES --}}
                <div class="flex-1 overflow-y-auto px-6 py-6 space-y-6 bg-[#F2F5F9]">

                    @php $allMessages = collect([$activeThread])->merge($activeThread->replies); @endphp

                    @foreach($allMessages as $message)
                        @php $isOwn = $message->sender_id === Auth::id(); @endphp

                        <div id="msg-{{ $message->id }}" class="{{ $isOwn ? 'flex flex-col items-end' : '' }}">

                            {{-- AVATAR + NAME ROW --}}
                            <div class="flex items-center gap-3 mb-2 {{ $isOwn ? 'flex-row-reverse' : '' }}">
                                <div class="w-8 h-8 rounded-lg flex items-center justify-center text-xs font-semibold flex-shrink-0
                                    {{ $isOwn ? 'bg-[#1B3A5C] text-white' : 'bg-[#C9A227] text-[#0F1B2D]' }}">
                                    {{ strtoupper(substr($message->sender->name, 0, 2)) }}
                                </div>
                                <div class="{{ $isOwn ? 'text-right' : '' }}">
                                    <p class="text-sm font-semibold text-slate-900">{{ $isOwn ? 'You' : $message->sender->name }}</p>
                                    <p class="text-xs text-slate-400">{{ ucfirst($message->sender->role) }}</p>
                                </div>
                                <span class="text-xs text-slate-400 {{ $isOwn ? 'mr-auto' : 'ml-auto' }}">
                                    {{ $message->created_at->format('D, d M · g:i A') }}
                                </span>
                            </div>

                            {{-- MESSAGE BUBBLE --}}
                            <div class="max-w-xl w-full">

                                {{-- NORMAL VIEW --}}
                                <div x-show="editingId !== {{ $message->id }} && deletingId !== {{ $message->id }}"
                                     class="group relative">

                                    <div id="msg-body-{{ $message->id }}"
                                         class="rounded-2xl px-4 py-3 text-sm leading-relaxed
                                         {{ $isOwn ? 'bg-[#1B3A5C] text-white rounded-tr-sm' : 'bg-white text-slate-800 rounded-tl-sm shadow-sm' }}">
                                        {{ $message->body }}
                                    </div>


                                    {{-- OWN MESSAGE ACTIONS (hover) --}}
                                    @if($isOwn)
                                        <div class="flex justify-end gap-1 mt-1.5 opacity-0 group-hover:opacity-100 transition-opacity">
                                            <button type="button"
                                                    @click="startEdit({{ $message->id }}, document.getElementById('msg-body-{{ $message->id }}').textContent.trim())"
                                                    class="text-[10px] font-medium text-slate-400 hover:text-[#1B3A5C] px-2 py-0.5 rounded hover:bg-[#E8EEF4] transition-colors">
                                                Edit
                                            </button>
                                            <button type="button"
                                                    @click="confirmDelete({{ $message->id }})"
                                                    class="text-[10px] font-medium text-slate-400 hover:text-rose-600 px-2 py-0.5 rounded hover:bg-rose-50 transition-colors">
                                                Delete
                                            </button>
                                        </div>
                                    @endif

                                </div>

                                {{-- EDIT MODE --}}
                                <div x-show="editingId === {{ $message->id }}" x-cloak style="display:none">
                                    <textarea x-model="editBody" rows="3"
                                              class="w-full rounded-xl bg-white border border-[#1B3A5C] px-4 py-3 text-sm text-slate-900
                                                     outline-none focus:ring-2 focus:ring-[#1B3A5C] transition-all resize-none"></textarea>
                                    <div class="flex justify-end gap-2 mt-1.5">
                                        <button type="button" @click="cancelEdit()"
                                                class="text-xs font-medium text-slate-500 border border-[#C8D3E0] rounded-lg px-3 py-1.5 hover:bg-[#E8EEF4] transition-colors">
                                            Cancel
                                        </button>
                                        <button type="button"
                                                @click="saveEdit({{ $message->id }}, '{{ csrf_token() }}', '{{ $msgBase }}')"
                                                class="text-xs font-medium text-white bg-[#1B3A5C] rounded-lg px-3 py-1.5 hover:bg-[#24507A] transition-colors">
                                            Save
                                        </button>
                                    </div>
                                </div>

                                {{-- DELETE CONFIRM --}}
                                <div x-show="deletingId === {{ $message->id }}" x-cloak style="display:none"
                                     class="rounded-xl bg-rose-50 border border-rose-200 px-4 py-3">
                                    <p class="text-sm font-medium text-rose-700 mb-3">Delete this message?</p>
                                    <div class="flex gap-2">
                                        <button type="button" @click="cancelDelete()"
                                                class="text-xs font-medium text-slate-600 border border-[#C8D3E0] rounded-lg px-3 py-1.5 hover:bg-[#E8EEF4] transition-colors">
                                            Cancel
                                        </button>
                                        <button type="button"
                                                @click="doDelete({{ $message->id }}, '{{ csrf_token() }}', '{{ $msgBase }}')"
                                                class="text-xs font-medium text-white bg-rose-600 rounded-lg px-3 py-1.5 hover:bg-rose-700 transition-colors">
                                            Yes, delete
                                        </button>
                                    </div>
                                </div>

                            </div>
                        </div>

                    @endforeach

                </div>

                {{-- REPLY BOX --}}
                <div class="border-t border-[#C8D3E0] px-6 py-4 flex-shrink-0">
                    <form method="POST" action="{{ $replyAction }}">
                        @csrf
                        <input type="hidden" name="parent_id" value="{{ $activeThread->id }}">
                        <input type="hidden" name="receiver_id" value="{{ $activeThread->sender_id === Auth::id() ? $activeThread->receiver_id : $activeThread->sender_id }}">
                        <input type="hidden" name="course_id" value="{{ $activeThread->course_id }}">
                        <input type="hidden" name="subject" value="{{ $activeThread->subject }}">
                        <textarea name="body" rows="3" placeholder="Write a reply..."
                                  class="w-full rounded-xl bg-[#E8EEF4] border border-[#C8D3E0] px-4 py-3 text-sm text-slate-900
                                         placeholder:text-slate-400 resize-none outline-none focus:ring-2 focus:ring-[#1B3A5C] focus:bg-white transition-all"></textarea>
                        <div class="flex justify-between items-center mt-3">
                            <span class="text-xs text-slate-400">
                                Replying to {{ $activeThread->sender_id === Auth::id() ? $activeThread->receiver->name : $activeThread->sender->name }}
                            </span>
                            <button type="submit"
                                    class="rounded-lg bg-[#1B3A5C] text-white text-sm font-medium px-5 py-2 hover:bg-[#24507A] transition-colors">
                                Send reply
                            </button>
                        </div>
                    </form>
                </div>

            @endif

        </div>

        {{-- EMPTY STATE --}}
        <div x-show="mode === 'empty'" class="flex-1 flex flex-col items-center justify-center gap-3 bg-[#F2F5F9]">
            <div class="w-12 h-12 rounded-2xl bg-[#E8EEF4] border border-[#C8D3E0] flex items-center justify-center">
                <svg class="w-6 h-6 text-[#1B3A5C]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                </svg>
            </div>
            <p class="text-sm font-medium text-slate-600">Select a conversation</p>
            <p class="text-xs text-slate-400">or click <button @click="openCompose()" class="text-[#1B3A5C] font-medium hover:underline">+ Compose</button> to start a new one</p>
        </div>

    </div>

</div>