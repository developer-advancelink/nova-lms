{{-- SHARED PROFILE PARTIAL --}}
@php
    $user = Auth::user();

    // Staff/Student ID and Department are never editable on your OWN profile,
    // whatever your role. They are set by an admin on /admin/users/{user}/edit,
    // and an admin has no department of their own to edit anyway. This used to
    // read `$user->role === 'admin'`, which was always false because no admin
    // role existed; now that one does, keep the fields locked deliberately
    // rather than offering an input the account endpoint silently ignores.
    $isAdmin = false;

    $initials = function (?string $name) {
        $words = array_filter(preg_split('/\s+/', trim($name ?? '')));
        if (count($words) <= 1) {
            return strtoupper(substr($name ?? '', 0, 2));
        }
        return strtoupper(substr(reset($words), 0, 1) . substr(end($words), 0, 1));
    };

    // Fields only admins can edit
    // These render as locked inputs for regular users
    $locked = 'bg-[#E8EEF4] border border-[#C8D3E0] text-slate-500 cursor-not-allowed';
    $editable = 'bg-[#E8EEF4] border border-[#C8D3E0] focus:ring-2 focus:ring-[#1B3A5C] focus:bg-white focus:border-[#1B3A5C]';
@endphp

<div class="rounded-2xl bg-white shadow-sm overflow-hidden max-w-2xl">

    {{-- HEADER --}}
    <div class="px-6 py-4 border-b border-[#C8D3E0] flex justify-between items-center">
        <h2 class="text-base font-semibold text-slate-900">Profile</h2>
        <button form="profile-form" type="submit"
                class="rounded-lg bg-[#1B3A5C] text-white text-sm font-medium px-4 py-2 hover:bg-[#24507A] transition-colors">
            Save Changes
        </button>
    </div>

    {{-- SUCCESS MESSAGE --}}
    @if(session('success'))
        <div class="px-6 pt-4">
            <div class="rounded-lg bg-emerald-50 border border-emerald-200 px-4 py-3 text-sm text-emerald-700">
                {{ session('success') }}
            </div>
        </div>
    @endif

    {{-- ERROR MESSAGES --}}
    @if($errors->any())
        <div class="px-6 pt-4">
            <div class="rounded-lg bg-rose-50 border border-rose-200 px-4 py-3 text-sm text-rose-700">
                <ul class="space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <form id="profile-form"
          method="POST"
          action="{{ $user->accountPath() }}">
        @csrf
        @method('PATCH')

        <div class="p-6 space-y-8">

            {{-- PROFILE PHOTO --}}
            <div class="flex items-center gap-5 pb-6 border-b border-[#C8D3E0]">

                <div class="w-16 h-16 rounded-xl bg-[#C9A227] text-[#0F1B2D] flex items-center justify-center text-2xl font-semibold flex-shrink-0">
                    {{ $initials($user->name) }}
                </div>

                <div>
                    <p class="text-sm font-medium text-slate-900 mb-0.5">Profile Photo</p>
                    <p class="text-xs text-slate-400 mb-3">JPG or PNG, max 2MB</p>
                    <button type="button"
                            class="text-xs font-medium text-slate-600 border border-[#C8D3E0] rounded-lg px-3 py-1.5 hover:bg-[#E8EEF4] transition-colors">
                        Upload Photo
                    </button>
                </div>

            </div>

            {{-- PERSONAL DETAILS --}}
            <div class="pb-6 border-b border-[#C8D3E0]">

                <p class="text-xs font-medium text-slate-400 uppercase tracking-wide mb-4">Personal Details</p>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                    {{-- FULL NAME — editable --}}
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1.5">Full Name</label>
                        <input type="text"
                               name="name"
                               value="{{ old('name', $user->name) }}"
                               class="w-full rounded-lg px-3 py-2.5 text-sm outline-none transition-all {{ $editable }}">
                    </div>

                    {{-- ID NUMBER — admin only --}}
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label class="text-xs font-medium text-slate-600">
                                {{ $user->role === 'lecturer' ? 'Staff ID' : 'Student ID' }}
                            </label>
                            @if(!$isAdmin)
                                <span class="text-[10px] font-medium text-slate-400 bg-[#E8EEF4] border border-[#C8D3E0] rounded-full px-2 py-0.5">
                                    Admin only
                                </span>
                            @endif
                        </div>
                        <input type="text"
                               name="id_number"
                               value="{{ old('id_number', $user->id_number) }}"
                               {{ !$isAdmin ? 'readonly' : '' }}
                               placeholder="{{ $user->role === 'lecturer' ? 'e.g. STAFF-001' : 'e.g. 2024-00182' }}"
                               class="w-full rounded-lg px-3 py-2.5 text-sm outline-none transition-all {{ !$isAdmin ? $locked : $editable }}">
                    </div>

                    {{-- EMAIL — editable --}}
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1.5">Email Address</label>
                        <input type="email"
                               name="email"
                               value="{{ old('email', $user->email) }}"
                               class="w-full rounded-lg px-3 py-2.5 text-sm outline-none transition-all {{ $editable }}">
                    </div>

                    {{-- DEPARTMENT / PROGRAM — admin only --}}
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label class="text-xs font-medium text-slate-600">
                                {{ $user->role === 'lecturer' ? 'Department' : 'Program' }}
                            </label>
                            @if(!$isAdmin)
                                <span class="text-[10px] font-medium text-slate-400 bg-[#E8EEF4] border border-[#C8D3E0] rounded-full px-2 py-0.5">
                                    Admin only
                                </span>
                            @endif
                        </div>
                        <input type="text"
                               name="department"
                               value="{{ old('department', $user->department) }}"
                               {{ !$isAdmin ? 'readonly' : '' }}
                               placeholder="{{ $user->role === 'lecturer' ? 'e.g. Computer Science' : 'e.g. BS Information Technology' }}"
                               class="w-full rounded-lg px-3 py-2.5 text-sm outline-none transition-all {{ !$isAdmin ? $locked : $editable }}">
                    </div>

                </div>

                {{-- BIO — editable --}}
                <div class="mt-4">
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">Bio</label>
                    <textarea name="bio"
                              rows="3"
                              class="w-full rounded-lg px-3 py-2.5 text-sm outline-none transition-all resize-none {{ $editable }}">{{ old('bio', $user->bio) }}</textarea>
                </div>

            </div>

            {{-- ROLE — always locked --}}
            <div class="pb-6 border-b border-[#C8D3E0]">

                <p class="text-xs font-medium text-slate-400 uppercase tracking-wide mb-4">Account</p>

                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label class="text-xs font-medium text-slate-600">Role</label>
                        <span class="text-[10px] font-medium text-slate-400 bg-[#E8EEF4] border border-[#C8D3E0] rounded-full px-2 py-0.5">
                            Admin only
                        </span>
                    </div>
                    <input type="text"
                           value="{{ ucfirst($user->role) }}"
                           readonly
                           class="w-full rounded-lg px-3 py-2.5 text-sm outline-none {{ $locked }}">
                </div>

            </div>

            {{-- COURSES --}}
            <div>
                <p class="text-xs font-medium text-slate-400 uppercase tracking-wide mb-3">
                    {{ $user->role === 'lecturer' ? 'Courses Teaching' : 'Enrolled Courses' }}
                </p>
                <div class="flex flex-wrap gap-2">
                    @forelse($courses as $course)
                        <span class="text-xs font-medium text-[#1B3A5C] bg-[#E8EEF4] border border-[#C8D3E0] rounded-full px-3 py-1">
                            {{ $course->code }} — {{ $course->name }}
                        </span>
                    @empty
                        <span class="text-xs text-slate-400">
                            {{ $user->role === 'lecturer' ? 'No courses assigned yet.' : 'Not enrolled in any courses yet.' }}
                        </span>
                    @endforelse
                </div>
            </div>

        </div>

    </form>

</div>