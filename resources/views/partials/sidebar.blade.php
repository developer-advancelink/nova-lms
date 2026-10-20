{{--
    SHARED SIDEBAR — NOVA (navy theme)
    Used by the student, lecturer and admin layouts.

    Expects (pass from the parent layout/controller):
      $role              'lecturer' | 'student' | 'admin'
      $basePath          '/lecturer', '/admin', or '' for student (no trailing slash)
      $courses           Collection of courses for the drawer
                            - lecturer: courses they teach
                            - student:  courses they're enrolled in
                            - admin:    empty (admin manages modules on its own screen)
      $coursesLabel      optional; defaults per role
      $navGroups         optional; ['Group label' => [ item, ... ]] where an item is
                            ['label' => 'Dashboard', 'href' => '/x', 'active' => bool]
                            or ['label' => 'Courses', 'drawer' => 'openCourses', 'active' => bool]
                         Defaults below reproduce the original student/lecturer nav exactly.
      $showCoursesDrawer optional; false hides the slide-out course drawer (admin)
--}}
@php
    $route = request()->path();
    $basePath = $basePath ?? '';
    $coursesLabel = $coursesLabel ?? ($role === 'lecturer' ? 'My Courses' : 'Enrolled');
    $showCoursesDrawer = $showCoursesDrawer ?? true;

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

    $navGroups = $navGroups ?? [
        'General' => [
            ['label' => 'Dashboard', 'href' => "{$basePath}/dashboard", 'active' => $isActive('dashboard')],
            ['label' => 'Courses',   'drawer' => 'openCourses',         'active' => $isSection('courses')],
            ['label' => 'Calendar',  'href' => "{$basePath}/calendar",  'active' => $isActive('calendar')],
            ['label' => 'Inbox',     'href' => "{$basePath}/inbox",     'active' => $isActive('inbox')],
        ],
        'System' => [
            // Links straight to the profile page, which now carries the
            // password form too — so there is nothing left for a drawer to
            // disambiguate.
            ['label' => 'Profile', 'href' => "{$basePath}/account", 'active' => $isSection('account')],
            ['label' => 'Help',    'href' => "{$basePath}/help",    'active' => $isActive('help')],
        ],
    ];
@endphp

{{-- Off-canvas below lg, pinned open from lg up. navOpen comes from the shell
     scope in layouts/app.blade.php. --}}
<aside
    x-data="{ openCourses: false }"
    :class="navOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
    class="fixed top-0 left-0 h-screen w-56 bg-[#0F1B2D] flex flex-col z-50
           transition-transform duration-200 ease-out -translate-x-full lg:translate-x-0"
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

        @foreach($navGroups as $groupLabel => $items)
            <div>
                <p class="text-[11px] font-medium uppercase tracking-wide px-3 mb-2 text-[#7C8AA0]">
                    {{ $groupLabel }}
                </p>
                <div class="space-y-1">

                    @foreach($items as $item)
                        @if(isset($item['drawer']))
                            <button
                                @click="{{ $item['drawer'] }} = true"
                                class="w-full flex items-center gap-3 px-3 py-2.5 rounded-sm text-left text-sm transition-colors {{ $navItem($item['active'] ?? false) }}">
                                <span>{{ $item['label'] }}</span>
                            </button>
                        @else
                            {{-- Closing navOpen matters on phones: without it the
                                 sidebar stays over the page it just navigated to. --}}
                            <a href="{{ $item['href'] }}"
                               @click="navOpen = false"
                               class="flex items-center gap-3 px-3 py-2.5 rounded-sm text-sm transition-colors {{ $navItem($item['active'] ?? false) }}">
                                <span>{{ $item['label'] }}</span>
                            </a>
                        @endif
                    @endforeach

                </div>
            </div>
        @endforeach

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
    @if($showCoursesDrawer)
    {{-- Sits beside the sidebar from lg up; on a phone there is no room for
         that, so it covers the screen instead. --}}
    <div
        x-cloak
        x-show="openCourses"
        x-transition
        class="fixed inset-y-0 left-0 w-full sm:w-80 lg:left-56 h-screen bg-[#E8EEF4] shadow-xl z-[9999]"
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
                       @click="navOpen = false"
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
    @endif

</aside>