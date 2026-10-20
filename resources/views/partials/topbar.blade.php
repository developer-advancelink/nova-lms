{{--
    SHARED TOPBAR
    Used by both lecturer and student layouts.
--}}
@php
    $quickActions = $quickActions ?? [];

    $initials = function (?string $name) {
        $words = array_filter(preg_split('/\s+/', trim($name ?? '')));
        if (count($words) <= 1) {
            return strtoupper(substr($name ?? '', 0, 2));
        }
        return strtoupper(substr(reset($words), 0, 1) . substr(end($words), 0, 1));
    };
@endphp

<header class="h-16 bg-[#E8EEF4] border-b border-[#C8D3E0] flex items-center justify-between gap-2 px-4 sm:px-6 lg:px-8 flex-shrink-0">

    {{-- LEFT: nav toggle + page title --}}
    <div class="flex items-center gap-3 min-w-0">

        {{-- Only needed where the sidebar is off-canvas. --}}
        <button type="button"
                @click="navOpen = true"
                aria-label="Open navigation"
                class="lg:hidden -ml-1 rounded-lg p-2 text-slate-600 hover:bg-[#D0D8E4] hover:text-slate-900 transition-colors">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </button>

        <h1 class="text-base sm:text-lg font-semibold text-slate-900 truncate">
            @yield('page-title', 'Dashboard')
        </h1>
    </div>

    {{-- RIGHT SIDE --}}
    <div class="flex items-center gap-2 sm:gap-3">

        {{-- SEARCH — hidden on phones, where it would crowd out the title --}}
        <div class="relative hidden md:block">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M11 19a8 8 0 100-16 8 8 0 000 16z" />
            </svg>
            <input type="text"
                   placeholder="Search..."
                   class="rounded-lg bg-[#E8EEF4] border border-[#C8D3E0] pl-9 pr-3 py-2 text-sm text-slate-700 placeholder:text-slate-400
                          outline-none focus:ring-2 focus:ring-sky-500 focus:bg-white transition-all w-48">
        </div>

        {{-- QUICK ACTIONS (lecturer only) --}}
        @if(count($quickActions) > 0)
            <div x-data="{ open: false }" class="relative">
                <button
                    @click="open = !open"
                    class="rounded-lg bg-[#1B3A5C] text-white px-3 sm:px-4 py-2 text-sm font-medium
                           whitespace-nowrap hover:bg-[#24507A] transition-colors">
                    <span class="sm:hidden">+</span>
                    <span class="hidden sm:inline">+ Quick actions</span>
                </button>
                <div
                    x-cloak
                    x-show="open"
                    @click.outside="open = false"
                    x-transition
                    class="absolute right-0 top-full mt-2 w-52 bg-white rounded-xl shadow-lg ring-1 ring-slate-200 z-50 overflow-hidden">
                    @foreach($quickActions as $i => $action)
                        <a href="{{ $action['href'] }}"
                           class="block px-4 py-3 text-sm text-slate-700 hover:bg-[#E8EEF4] transition-colors {{ $i > 0 ? 'border-t border-slate-100' : '' }}">
                            {{ $action['label'] }}
                        </a>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- NOTIFICATIONS --}}
        <button class="relative rounded-lg p-2 text-slate-500 hover:bg-[#D0D8E4] hover:text-slate-700 transition-colors">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M15 17h5l-1.4-1.4A2 2 0 0118 14.2V11a6 6 0 10-12 0v3.2a2 2 0 01-.6 1.4L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
            </svg>
            @if(($unreadNotifications ?? 0) > 0)
                <span class="absolute top-1 right-1 w-2 h-2 rounded-full bg-amber-500 ring-2 ring-[#E8EEF4]"></span>
            @endif
        </button>

        {{-- DIVIDER --}}
        <div class="hidden sm:block w-px h-6 bg-[#C8D3E0]"></div>

        {{-- USER — the name and role drop away on phones; the sidebar's user
             card still carries both. --}}
        <div class="flex items-center gap-2.5">
            <div class="w-8 h-8 rounded-lg bg-[#C9A227] text-[#0F1B2D] flex items-center justify-center text-xs font-semibold flex-shrink-0">
                {{ $initials(Auth::user()->name) }}
            </div>
            <div class="leading-tight hidden sm:block">
                <p class="text-sm font-medium text-slate-900">{{ Auth::user()->name }}</p>
                <p class="text-xs text-slate-400">{{ ucfirst(Auth::user()->role) }}</p>
            </div>
        </div>

    </div>

</header>