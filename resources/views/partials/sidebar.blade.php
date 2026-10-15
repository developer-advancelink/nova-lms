{{--
    SHARED SIDEBAR — NOVA (navy theme)
    Used by both lecturer and student layouts.

    Expects (pass from the parent layout/controller):
      $role          'lecturer' | 'student'
      $basePath      '/lecturer'  for lecturer, ''  for student  (no trailing slash)
      $courses       Collection of courses to show in the drawer
                        - lecturer: courses they teach
                        - student:  courses they're enrolled in
      $coursesLabel  'My Courses' (lecturer) or 'Enrolled' (student) — optional, sensible default below
--}}
@php
    $route = request()->path();
    $basePath = $basePath ?? '';
    $coursesLabel = $coursesLabel ?? ($role === 'lecturer' ? 'My Courses' : 'Enrolled');

    $isActive = fn ($path) => $route === ltrim("{$basePath}/{$path}", '/');
    $isSection = fn ($needle) => str_contains($route, $needle);

    $initials = function (?string $name) {
        $words = array_filter(preg_split('/\s+/', trim($name ?? '')));
        if (count($words) <= 1) {
            return strtoupper(substr($name ?? '', 0, 2));
        }
        return strtoupper(substr(reset($words), 0, 1) . substr(end($words), 0, 1));
    };

    $navItem = fn ($active) => $active
        ? 'bg-[#1B2D47] text-white font-medium'
        : 'text-[#A8B3C4] hover:text-white hover:bg-[#16243A]';
@endphp

<aside
    x-data="{ openCourses: false, openAccount: false }"
    class="fixed top-0 left-0 h-screen w-64 bg-[#0F1B2D] flex flex-col z-50"
>

    {{-- LOGO --}}
    <div class="h-16 flex items-center px-6 border-b border-[#26344A]">
        <a href="{{ $basePath ?: '/' }}/dashboard" class="block">
            <p class="text-lg font-semibold tracking-[0.15em] text-white leading-none">NOVA</p>
            <p class="text-[8px] tracking-[0.18em] uppercase text-[#7C8AA0] mt-1 leading-none">
                Networked Online Virtual Academy
            </p>
        </a>
    </div>

    {{-- NAVIGATION --}}
    <nav class="flex-1 overflow-y-auto px-3 py-5 space-y-6">

        {{-- GENERAL --}}
        <div>
            <p class="text-[11px] font-medium uppercase tracking-wide px-3 mb-2 text-[#7C8AA0]">
                General
            </p>
            <div class="space-y-1">

                <a href="{{ $basePath }}/dashboard"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-sm text-sm transition-colors {{ $navItem($isActive('dashboard')) }}">
                    <span>Dashboard</span>
                </a>

                <button
                    @click="openCourses = true; openAccount = false"
                    class="w-full flex items-center gap-3 px-3 py-2.5 rounded-sm text-left text-sm transition-colors {{ $navItem($isSection('courses')) }}">
                    <span>Courses</span>
                </button>

                <a href="{{ $basePath }}/calendar"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-sm text-sm transition-colors {{ $navItem($isActive('calendar')) }}">
                    <span>Calendar</span>
                </a>

                <a href="{{ $basePath }}/inbox"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-sm text-sm transition-colors {{ $navItem($isActive('inbox')) }}">
                    <span>Inbox</span>
                </a>

            </div>
        </div>

        {{-- SYSTEM --}}
        <div>
            <p class="text-[11px] font-medium uppercase tracking-wide px-3 mb-2 text-[#7C8AA0]">
                System
            </p>
            <div class="space-y-1">

                <button
                    @click="openAccount = true; openCourses = false"
                    class="w-full flex items-center gap-3 px-3 py-2.5 rounded-sm text-left text-sm transition-colors {{ $navItem($isSection('account')) }}">
                    <span>Account</span>
                </button>

                <a href="{{ $basePath }}/help"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-sm text-sm transition-colors {{ $navItem($isActive('help')) }}">
                    <span>Help</span>
                </a>

            </div>
        </div>

    </nav>

    {{-- USER SECTION --}}
    <div class="border-t border-[#26344A] p-4 space-y-2">

        <div class="rounded-sm p-3 flex items-center gap-3 bg-[#16243A]">
            <div class="w-9 h-9 rounded-sm flex items-center justify-center text-xs font-semibold flex-shrink-0 bg-[#C9A227] text-[#0F1B2D]">
                {{ $initials(Auth::user()->name) }}
            </div>
            <div class="min-w-0">
                <p class="text-sm font-medium text-white truncate">{{ Auth::user()->name }}</p>
                <p class="text-xs text-[#7C8AA0]">{{ ucfirst(Auth::user()->role) }}</p>
            </div>
        </div>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit"
                    class="w-full text-xs font-medium rounded-sm px-4 py-2.5 transition-colors text-[#A8B3C4] hover:text-white hover:bg-[#16243A]">
                Log out
            </button>
        </form>

    </div>

    {{-- COURSES DRAWER --}}
    <div
        x-cloak
        x-show="openCourses"
        x-transition
        class="fixed left-64 top-0 w-80 h-screen bg-[#E8EEF4] shadow-xl z-[9999]"
    >

        <div class="flex items-center justify-between px-5 h-16 border-b border-[#C8D3E0]">
            <h2 class="font-semibold text-slate-900">Courses</h2>
            <button @click="openCourses = false"
                    class="rounded-sm p-1.5 text-slate-400 hover:bg-[#C8D3E0] hover:text-slate-700 transition-colors">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <div class="p-5 space-y-5 text-sm">

            <a href="{{ $basePath }}/courses"
               class="block rounded-lg bg-white border border-[#C8D3E0] px-4 py-3 font-medium text-[#1B3A5C] hover:bg-[#F0F4F8] transition-colors">
                All courses
            </a>

            <div>
                <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-widest mb-2">{{ $coursesLabel }}</p>

                @forelse(($courses ?? collect()) as $course)
                    <a href="{{ $basePath }}/courses/{{ $course->id }}"
                       class="block rounded-lg bg-white border border-[#C8D3E0] px-4 py-3 mb-2 last:mb-0 hover:bg-[#F0F4F8] transition-colors">
                        <p class="font-medium text-slate-800">{{ $course->code }}</p>
                        <p class="text-xs text-slate-500 mt-0.5">{{ $course->name }}</p>
                    </a>
                @empty
                    <p class="text-xs text-slate-400">
                        {{ $role === 'lecturer' ? 'No courses assigned yet.' : 'No enrolled courses yet.' }}
                    </p>
                @endforelse
            </div>

        </div>

    </div>

    {{-- ACCOUNT DRAWER --}}
    <div
        x-cloak
        x-show="openAccount"
        x-transition
        class="fixed left-64 top-0 w-80 h-screen bg-[#E8EEF4] shadow-xl z-[9999]"
    >

        <div class="flex items-center justify-between px-5 h-16 border-b border-[#C8D3E0]">
            <h2 class="font-semibold text-slate-900">Account</h2>
            <button @click="openAccount = false"
                    class="rounded-sm p-1.5 text-slate-400 hover:bg-[#C8D3E0] hover:text-slate-700 transition-colors">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        {{-- USER INFO SECTION --}}
        <div class="p-5 border-b border-[#C8D3E0]">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 rounded-lg flex items-center justify-center text-sm font-semibold flex-shrink-0 bg-[#C9A227] text-[#0F1B2D]">
                    {{ $initials(Auth::user()->name) }}
                </div>
                <div>
                    <p class="text-sm font-semibold text-slate-900">{{ Auth::user()->name }}</p>
                    <p class="text-xs text-slate-500">{{ ucfirst(Auth::user()->role) }}</p>
                </div>
            </div>
        </div>

        {{-- ACCOUNT LINKS --}}
        <div class="p-3 space-y-0.5 text-sm">

            @php
                $accountLinks = [
                    'account'          => 'Profile',
                    'account/password' => 'Password',
                ];
            @endphp

            @foreach($accountLinks as $path => $label)
                <a href="{{ $basePath }}/{{ $path }}"
                   class="block rounded-lg px-4 py-2.5 transition-colors
                   {{ $isActive($path)
                       ? 'bg-white border border-[#C8D3E0] text-[#1B3A5C] font-semibold'
                       : 'text-slate-600 hover:bg-white hover:border hover:border-[#C8D3E0] hover:text-slate-900' }}">
                    {{ $label }}
                </a>
            @endforeach

        </div>

    </div>

</aside>