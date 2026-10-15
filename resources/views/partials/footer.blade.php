{{-- NOVA FOOTER — role-aware --}}
@php $isAdmin = Auth::check() && Auth::user()->role === 'admin'; @endphp

@if(!$isAdmin)
{{-- MAIN FOOTER — shown to lecturers and students --}}
<footer class="bg-[#0F1B2D] mt-auto">

    <div class="px-10 py-8 grid grid-cols-1 md:grid-cols-3 gap-8 border-b border-[#26344A]">

        {{-- LEFT — BRANDING + CONTACT --}}
        <div class="space-y-4">
            <div>
                <p class="text-lg font-semibold tracking-[0.15em] text-white leading-none">NOVA</p>
                <p class="text-[8px] tracking-[0.18em] uppercase text-[#7C8AA0] mt-1 leading-none">
                    Networked Online Virtual Academy
                </p>
            </div>
            <p class="text-xs text-[#7C8AA0] leading-relaxed">
                A digital learning platform developed for the Ministry of Defence, Brunei Darussalam.
            </p>
            <div class="rounded-lg border border-[#26344A] p-4 space-y-2">
                <p class="text-[10px] font-semibold uppercase tracking-widest text-[#C9A227] mb-3">Contact Info</p>
                <div class="flex items-center gap-3 text-xs text-[#A8B3C4]">
                    <svg class="w-3.5 h-3.5 text-[#C9A227] flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                    <span>nova@mindef.gov.bn</span>
                </div>
                <div class="flex items-center gap-3 text-xs text-[#A8B3C4]">
                    <svg class="w-3.5 h-3.5 text-[#C9A227] flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                    </svg>
                    <span>+673 238 2323</span>
                </div>
                <div class="flex items-start gap-3 text-xs text-[#A8B3C4]">
                    <svg class="w-3.5 h-3.5 text-[#C9A227] flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <span>Ministry of Defence, Berakas, Brunei</span>
                </div>
            </div>
        </div>

        {{-- MIDDLE — PLATFORM FEATURES --}}
        <div>
            <p class="text-[10px] font-semibold uppercase tracking-widest text-[#C9A227] mb-5">Platform Features</p>
            <ul class="space-y-4">
                @foreach([
                    ['Course & Module Management', '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />'],
                    ['Assignment Submission & Grading', '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />'],
                    ['Announcements & Messaging', '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />'],
                    ['Grade Tracking & Feedback', '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />'],
                    ['Calendar & Scheduling', '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />'],
                ] as [$label, $path])
                    <li class="flex items-center gap-3 text-sm text-[#A8B3C4]">
                        <span class="flex-shrink-0 w-7 h-7 rounded-md bg-[#16243A] border border-[#26344A] flex items-center justify-center">
                            <svg class="w-3.5 h-3.5 text-[#C9A227]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                {!! $path !!}
                            </svg>
                        </span>
                        {{ $label }}
                    </li>
                @endforeach
            </ul>
        </div>

        {{-- RIGHT — OPERATIONS --}}
        <div>
            <p class="text-[10px] font-semibold uppercase tracking-widest text-[#C9A227] mb-5">Operations</p>
            <ul class="space-y-4">
                @foreach([
                    ['Role-based Access Control', '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />'],
                    ['Lecturer & Student Portals', '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />'],
                    ['Real-time Submission Tracking', '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />'],
                    ['Secure Document Uploads', '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />'],
                    ['Administrative Oversight', '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />'],
                ] as [$label, $path])
                    <li class="flex items-center gap-3 text-sm text-[#A8B3C4]">
                        <span class="flex-shrink-0 w-7 h-7 rounded-md bg-[#16243A] border border-[#26344A] flex items-center justify-center">
                            <svg class="w-3.5 h-3.5 text-[#C9A227]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                {!! $path !!}
                            </svg>
                        </span>
                        {{ $label }}
                    </li>
                @endforeach
            </ul>
        </div>

    </div>

    {{-- COPYRIGHT BAR --}}
    <div class="px-10 py-3 flex flex-col sm:flex-row items-center justify-between gap-2">
        <p class="text-[10px] text-[#7C8AA0]">&copy; {{ date('Y') }} AdvancelinkCompany. All Rights Reserved.</p>
        <div class="flex items-center gap-1 text-[#7C8AA0]">
            <a href="/privacy"  class="text-[10px] hover:text-white transition-colors">Privacy Policy</a>
            <span class="text-[#26344A] mx-1">·</span>
            <a href="/terms"    class="text-[10px] hover:text-white transition-colors">Terms of Use</a>
            <span class="text-[#26344A] mx-1">·</span>
            <a href="/security" class="text-[10px] hover:text-white transition-colors">Security</a>
        </div>
    </div>

</footer>

@else

{{-- ADMIN FOOTER — copyright bar only --}}
<footer class="bg-[#0F1B2D] border-t border-[#26344A] px-8 py-3 flex flex-col sm:flex-row items-center justify-between gap-2">
    <p class="text-[10px] text-[#7C8AA0]">&copy; {{ date('Y') }} AdvancelinkCompany. All Rights Reserved.</p>
    <div class="flex items-center gap-1 text-[#7C8AA0]">
        <a href="/privacy"  class="text-[10px] hover:text-white transition-colors">Privacy Policy</a>
        <span class="text-[#26344A] mx-1">·</span>
        <a href="/terms"    class="text-[10px] hover:text-white transition-colors">Terms of Use</a>
        <span class="text-[#26344A] mx-1">·</span>
        <a href="/security" class="text-[10px] hover:text-white transition-colors">Security</a>
    </div>
</footer>

@endif