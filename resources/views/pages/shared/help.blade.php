@extends('layouts.app')

@section('title', 'Help & Support')
@section('page-title', 'Help & Support')

@section('content')

@php $isLecturer = Auth::user()->role === 'lecturer'; @endphp

<div class="space-y-6">

    {{-- HERO SEARCH --}}
    <div class="rounded-2xl bg-[#1B3A5C] p-8 text-white">
        <h2 class="text-2xl font-semibold mb-1">How can we help you?</h2>
        <p class="text-sm text-[#7C9EC0] mb-6">Search our help articles or browse by topic below.</p>
        <div class="relative max-w-lg">
            <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-[#7C9EC0]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M11 19a8 8 0 100-16 8 8 0 000 16z" />
            </svg>
            <input type="text"
                   placeholder="Search help articles..."
                   class="w-full rounded-lg bg-[#0F2744] border border-[#26527A] pl-12 pr-4 py-3 text-sm text-white
                          placeholder:text-[#7C9EC0] outline-none focus:border-[#C9A227] transition-all">
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

        {{-- LEFT: MAIN HELP CONTENT --}}
        <div class="xl:col-span-2 space-y-6">

            {{-- GETTING STARTED --}}
            <div class="rounded-2xl bg-white border border-[#C8D3E0] overflow-hidden">
                <div class="px-6 py-4 border-b border-[#E8EEF4] flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-[#E8EEF4] border border-[#C8D3E0] flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4 text-[#1B3A5C]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </div>
                    <h3 class="text-base font-semibold text-slate-900">Getting Started</h3>
                </div>
                <div x-data="{ open: null }" class="divide-y divide-[#E8EEF4]">
                    @foreach([
                        ['How do I log in to NOVA?', 'Navigate to the NOVA login page and enter your institutional email address and password. If you have forgotten your password, use the "Forgot Password" link on the login page. Contact your system administrator if you do not have login credentials yet.'],
                        ['How do I update my profile information?', 'Click on "Account" in the left sidebar to open the account drawer, then select "Profile". You can update your name, email address, and bio. Note that your Staff/Student ID, Department, and Role can only be changed by an administrator.'],
                        ['How do I change my password?', 'Go to Account → Password from the sidebar. Enter your current password, then your new password. Your new password must be at least 12 characters and include uppercase, lowercase, a number, and a symbol.'],
                    ] as $i => [$q, $a])
                    <div>
                        <button @click="open = open === {{ $i }} ? null : {{ $i }}"
                                class="w-full flex items-center justify-between px-6 py-4 text-sm font-medium text-slate-800 hover:bg-[#E8EEF4] transition-colors text-left">
                            <span>{{ $q }}</span>
                            <svg class="w-4 h-4 text-slate-400 flex-shrink-0 ml-4 transition-transform"
                                 :class="open === {{ $i }} ? 'rotate-180' : ''"
                                 fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div x-show="open === {{ $i }}" x-cloak class="px-6 pb-4 text-sm text-slate-500 leading-relaxed">
                            {{ $a }}
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- ROLE-SPECIFIC FAQ --}}
            <div class="rounded-2xl bg-white border border-[#C8D3E0] overflow-hidden">
                <div class="px-6 py-4 border-b border-[#E8EEF4] flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-[#E8EEF4] border border-[#C8D3E0] flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4 text-[#1B3A5C]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                    </div>
                    <h3 class="text-base font-semibold text-slate-900">
                        {{ $isLecturer ? 'Managing Courses & Students' : 'Courses & Assignments' }}
                    </h3>
                </div>
                <div x-data="{ open: null }" class="divide-y divide-[#E8EEF4]">
                    @if($isLecturer)
                        @foreach([
                            ['How do I upload course materials?', 'Open a course from the Courses page, then navigate to the Modules section. Fill in the week number, title, and type (Notes, Tutorial, or Slides), then select a file (PDF, DOCX, or PPTX up to 10MB) and click Upload.'],
                            ['How do I create an assignment?', 'Open the course, go to the Assignments section, fill in the title, optional description, and due date, then click Create Assignment. Students will immediately be able to see the assignment.'],
                            ['How do I grade student submissions?', 'Go to the Grades section within a course. Each assignment is listed with all enrolled students. Enter a grade and optional feedback for each student, then click Save. Grades are saved individually per student.'],
                            ['How do I post an announcement?', 'Open a course and go to the Announcements section. Enter a title and body text, then click Post Announcement. All enrolled students will see it immediately on their course home page.'],
                        ] as $i => [$q, $a])
                        <div>
                            <button @click="open = open === {{ $i }} ? null : {{ $i }}"
                                    class="w-full flex items-center justify-between px-6 py-4 text-sm font-medium text-slate-800 hover:bg-[#E8EEF4] transition-colors text-left">
                                <span>{{ $q }}</span>
                                <svg class="w-4 h-4 text-slate-400 flex-shrink-0 ml-4 transition-transform"
                                     :class="open === {{ $i }} ? 'rotate-180' : ''"
                                     fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                            <div x-show="open === {{ $i }}" x-cloak class="px-6 pb-4 text-sm text-slate-500 leading-relaxed">
                                {{ $a }}
                            </div>
                        </div>
                        @endforeach
                    @else
                        @foreach([
                            ['How do I access my courses?', 'Click Courses in the sidebar to see all your enrolled courses. You can star a course to mark it as a favourite — starred courses appear at the top of the list and in a dedicated card view.'],
                            ['How do I view my assignments?', 'Open a course and click Assignments in the course navigation. Each assignment shows its due date — assignments due today are highlighted in amber. Closed assignments are greyed out.'],
                            ['How do I check my grades?', 'Open a course and go to the Grades section. You will see all your submitted assignments with the grade and any feedback your lecturer has provided. Pending means your lecturer has not graded it yet.'],
                            ['How do I download course materials?', 'Open a course and go to Modules. Each uploaded material has a Download button. Click it to open or download the file in a new tab.'],
                        ] as $i => [$q, $a])
                        <div>
                            <button @click="open = open === {{ $i }} ? null : {{ $i }}"
                                    class="w-full flex items-center justify-between px-6 py-4 text-sm font-medium text-slate-800 hover:bg-[#E8EEF4] transition-colors text-left">
                                <span>{{ $q }}</span>
                                <svg class="w-4 h-4 text-slate-400 flex-shrink-0 ml-4 transition-transform"
                                     :class="open === {{ $i }} ? 'rotate-180' : ''"
                                     fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                            <div x-show="open === {{ $i }}" x-cloak class="px-6 pb-4 text-sm text-slate-500 leading-relaxed">
                                {{ $a }}
                            </div>
                        </div>
                        @endforeach
                    @endif
                </div>
            </div>

            {{-- INBOX & CALENDAR --}}
            <div class="rounded-2xl bg-white border border-[#C8D3E0] overflow-hidden">
                <div class="px-6 py-4 border-b border-[#E8EEF4] flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-[#E8EEF4] border border-[#C8D3E0] flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4 text-[#1B3A5C]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                        </svg>
                    </div>
                    <h3 class="text-base font-semibold text-slate-900">Inbox & Calendar</h3>
                </div>
                <div x-data="{ open: null }" class="divide-y divide-[#E8EEF4]">
                    @foreach([
                        ['How do I send a message?', 'Open the Inbox from the sidebar and click "+ Compose" to start a new conversation. Select the recipient, enter a subject and message body, then send. You can also reply to existing threads by opening a conversation.'],
                        ['How do I use the Calendar?', 'The Calendar shows all assignment due dates for your courses in the current month. Use the Prev and Next buttons to navigate between months. The right panel shows upcoming due dates in chronological order.'],
                    ] as $i => [$q, $a])
                    <div>
                        <button @click="open = open === {{ $i }} ? null : {{ $i }}"
                                class="w-full flex items-center justify-between px-6 py-4 text-sm font-medium text-slate-800 hover:bg-[#E8EEF4] transition-colors text-left">
                            <span>{{ $q }}</span>
                            <svg class="w-4 h-4 text-slate-400 flex-shrink-0 ml-4 transition-transform"
                                 :class="open === {{ $i }} ? 'rotate-180' : ''"
                                 fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div x-show="open === {{ $i }}" x-cloak class="px-6 pb-4 text-sm text-slate-500 leading-relaxed">
                            {{ $a }}
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

        </div>

        {{-- RIGHT: CONTACT & QUICK LINKS --}}
        <div class="space-y-6">

            {{-- CONTACT SUPPORT --}}
            <div class="rounded-2xl bg-white border border-[#C8D3E0] overflow-hidden">
                <div class="px-6 py-4 border-b border-[#E8EEF4]">
                    <h3 class="text-base font-semibold text-slate-900">Contact Support</h3>
                    <p class="text-xs text-slate-400 mt-0.5">Can't find what you're looking for?</p>
                </div>
                <div class="p-5 space-y-4">
                    <a href="mailto:nova@mindef.gov.bn"
                       class="flex items-center gap-3 rounded-lg bg-[#E8EEF4] border border-[#C8D3E0] px-4 py-3 hover:bg-[#D8E3EF] transition-colors group">
                        <div class="w-8 h-8 rounded-md bg-[#1B3A5C] flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-slate-700">Email Support</p>
                            <p class="text-xs text-slate-400">nova@mindef.gov.bn</p>
                        </div>
                    </a>

                    <a href="tel:+6732382323"
                       class="flex items-center gap-3 rounded-lg bg-[#E8EEF4] border border-[#C8D3E0] px-4 py-3 hover:bg-[#D8E3EF] transition-colors group">
                        <div class="w-8 h-8 rounded-md bg-[#1B3A5C] flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-slate-700">Phone Support</p>
                            <p class="text-xs text-slate-400">+673 238 2323</p>
                        </div>
                    </a>

                    <div class="flex items-center gap-3 rounded-lg bg-[#E8EEF4] border border-[#C8D3E0] px-4 py-3">
                        <div class="w-8 h-8 rounded-md bg-[#1B3A5C] flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-slate-700">Location</p>
                            <p class="text-xs text-slate-400">Ministry of Defence, Berakas</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- QUICK LINKS --}}
            <div class="rounded-2xl bg-white border border-[#C8D3E0] overflow-hidden">
                <div class="px-6 py-4 border-b border-[#E8EEF4]">
                    <h3 class="text-base font-semibold text-slate-900">Quick Links</h3>
                </div>
                <div class="p-3 space-y-1">
                    @php
                        $base = $isLecturer ? '/lecturer' : '';
                        $links = [
                            ['Dashboard',       $base . '/dashboard'],
                            ['My Courses',      $base . '/courses'],
                            ['Inbox',           $base . '/inbox'],
                            ['Calendar',        $base . '/calendar'],
                            ['My Profile',      $base . '/account'],
                            ['Change Password', $base . '/account/password'],
                        ];
                    @endphp
                    @foreach($links as [$label, $href])
                        <a href="{{ $href }}"
                           class="flex items-center justify-between px-4 py-2.5 rounded-lg text-sm text-slate-600 hover:bg-[#E8EEF4] hover:text-[#1B3A5C] transition-colors group">
                            <span>{{ $label }}</span>
                            <svg class="w-3.5 h-3.5 text-slate-300 group-hover:text-[#1B3A5C] transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </a>
                    @endforeach
                </div>
            </div>

            {{-- SYSTEM INFO --}}
            <div class="rounded-2xl bg-white border border-[#C8D3E0] p-5">
                <p class="text-[10px] font-semibold uppercase tracking-widest text-slate-400 mb-3">System Info</p>
                <div class="space-y-2 text-xs text-slate-500">
                    <div class="flex justify-between">
                        <span class="text-slate-400">Platform</span>
                        <span class="font-medium text-slate-700">NOVA E-LMS</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-400">Operated by</span>
                        <span class="font-medium text-slate-700">AdvancelinkCompany</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-400">Client</span>
                        <span class="font-medium text-slate-700">MINDEF Brunei</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-400">Logged in as</span>
                        <span class="font-medium text-slate-700">{{ ucfirst(Auth::user()->role) }}</span>
                    </div>
                </div>
            </div>

        </div>

    </div>

</div>

@endsection