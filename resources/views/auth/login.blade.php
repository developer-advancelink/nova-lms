<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — NOVA</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body x-data="{ activeModal: null }" class="min-h-screen flex flex-col overflow-y-auto bg-[#0F1B2D]">

    {{-- LOGIN CONTENT --}}
    <div class="flex-1 flex items-center justify-center px-4 py-10">
        <div class="w-full max-w-sm">

            {{-- WORDMARK --}}
            <div class="mb-10 text-center">
                <h1 class="text-4xl font-semibold tracking-[0.15em] text-white">NOVA</h1>
                <p class="text-[11px] tracking-[0.2em] uppercase text-slate-400 mt-2">
                    Networked Online Virtual Academy
                </p>
            </div>

            {{-- CARD --}}
            <div class="rounded-sm p-8 bg-[#F5F3EE]">

                {{-- SESSION STATUS --}}
                @if(session('status'))
                    <div class="mb-5 rounded-sm bg-emerald-50 ring-1 ring-emerald-300 px-4 py-3 text-sm text-emerald-800">
                        {{ session('status') }}
                    </div>
                @endif

                {{-- VALIDATION ERRORS --}}
                @if($errors->any())
                    <div class="mb-5 rounded-sm bg-rose-50 ring-1 ring-rose-300 px-4 py-3 text-sm text-rose-800">
                        <ul class="space-y-1">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- FORM --}}
                <form method="POST" action="{{ route('login') }}" class="space-y-5">

                    @csrf

                    {{-- EMAIL --}}
                    <div>
                        <label for="email" class="block text-[11px] font-semibold tracking-wide uppercase text-slate-600 mb-1.5">
                            Email address
                        </label>
                        <input type="email"
                               name="email"
                               id="email"
                               value="{{ old('email') }}"
                               required
                               autofocus
                               class="w-full rounded-sm bg-white ring-1 ring-slate-300 px-3 py-2.5 text-sm text-slate-900
                                      outline-none focus:ring-2 focus:ring-[#0F1B2D] transition-all">
                    </div>

                    {{-- PASSWORD --}}
                    <div>
                        <label for="password" class="block text-[11px] font-semibold tracking-wide uppercase text-slate-600 mb-1.5">
                            Password
                        </label>
                        <input type="password"
                               name="password"
                               id="password"
                               required
                               class="w-full rounded-sm bg-white ring-1 ring-slate-300 px-3 py-2.5 text-sm text-slate-900
                                      outline-none focus:ring-2 focus:ring-[#0F1B2D] transition-all">
                    </div>

                    {{-- REMEMBER ME --}}
                    <div class="flex items-center justify-between pt-1">
                        <label for="remember" class="flex items-center gap-2 text-xs text-slate-600 cursor-pointer">
                            <input type="checkbox"
                                   name="remember"
                                   id="remember"
                                   class="rounded-sm border-slate-400 text-[#0F1B2D] focus:ring-[#0F1B2D]">
                            Remember me
                        </label>

                        <button type="button"
                                @click="activeModal = 'forgot'"
                                class="text-xs font-medium text-slate-600 hover:text-slate-900 underline underline-offset-2">
                            Forgot password?
                        </button>
                    </div>

                    {{-- SUBMIT --}}
                    <button type="submit"
                            class="w-full rounded-sm py-2.5 text-sm font-semibold tracking-wide uppercase text-white transition-colors
                                   bg-[#0F1B2D] hover:bg-[#1B2D47]">
                        Sign in
                    </button>

                </form>

            </div>

            {{-- REGISTER LINK --}}
            <p class="text-center text-xs text-slate-500 mt-6">
                Don't have an account?
                <button type="button"
                        @click="activeModal = 'register'"
                        class="font-medium text-[#C9A227] hover:underline">
                    Register
                </button>
            </p>

        </div>
    </div>

    {{-- FOOTER --}}
    @include('partials.login-footer')

    {{-- MODAL BACKDROP + PANELS --}}
    <div x-cloak
         x-show="activeModal !== null"
         x-transition.opacity
         @keydown.escape.window="activeModal = null"
         class="fixed inset-0 z-50 flex items-center justify-center px-4 bg-black/60">

        {{-- click outside to close --}}
        <div class="absolute inset-0" @click="activeModal = null"></div>

        {{-- REGISTER MODAL --}}
        <div x-show="activeModal === 'register'"
             x-transition
             @click.outside="activeModal = null"
             class="relative w-full max-w-sm rounded-sm p-8 bg-[#F5F3EE]">

            <button type="button"
                    @click="activeModal = null"
                    class="absolute top-4 right-4 text-slate-400 hover:text-slate-700">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>

            <div class="flex justify-center mb-5">
                <div class="w-12 h-12 rounded-full bg-[#0F1B2D] flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-[#C9A227]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                </div>
            </div>

            <h2 class="text-center text-sm font-semibold tracking-wide uppercase text-slate-800 mb-2">
                Account Registration
            </h2>

            <p class="text-center text-sm text-slate-600 leading-relaxed mb-6">
                Accounts on NOVA are created and managed by your administrator.
                Please reach out with your name and role to request access.
            </p>

            <div class="rounded-sm bg-white ring-1 ring-slate-300 px-4 py-4 space-y-3 mb-6">
                <div class="flex items-start gap-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mt-0.5 text-[#0F1B2D] shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                    </svg>
                    <div>
                        <p class="text-[11px] font-semibold tracking-wide uppercase text-slate-500">Email</p>
                        <a href="mailto:admin@nova.mindef.gov.bn" class="text-sm text-slate-800 hover:text-[#0F1B2D]">
                            admin@nova.mindef.gov.bn
                        </a>
                    </div>
                </div>

                <div class="flex items-start gap-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mt-0.5 text-[#0F1B2D] shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a1.5 1.5 0 001.5-1.5v-3.028a1.5 1.5 0 00-1.212-1.472l-4.493-.899a1.5 1.5 0 00-1.541.67l-.622 1.036a12.032 12.032 0 01-6.16-6.16l1.036-.622a1.5 1.5 0 00.67-1.541l-.899-4.493A1.5 1.5 0 007.028 3H4.5a1.5 1.5 0 00-1.5 1.5v2.25z" />
                    </svg>
                    <div>
                        <p class="text-[11px] font-semibold tracking-wide uppercase text-slate-500">Phone</p>
                        <a href="tel:+6732000000" class="text-sm text-slate-800 hover:text-[#0F1B2D]">
                            +673 2XX XXXX
                        </a>
                    </div>
                </div>

                <div class="flex items-start gap-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mt-0.5 text-[#0F1B2D] shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 6.75V15m6-6v8.25m.503 3.498l4.875-2.437c.381-.19.622-.58.622-1.006V4.82c0-.836-.88-1.38-1.628-1.006l-3.869 1.934c-.317.159-.69.159-1.006 0L9.503 3.804a1.125 1.125 0 00-1.006 0L3.622 6.24C3.24 6.43 3 6.822 3 7.247v10.006c0 .836.88 1.38 1.628 1.006l3.869-1.934c.317-.159.69-.159 1.006 0l4.994 2.497c.317.158.69.158 1.006 0z" />
                    </svg>
                    <div>
                        <p class="text-[11px] font-semibold tracking-wide uppercase text-slate-500">Office</p>
                        <p class="text-sm text-slate-800">MINDEF, Bandar Seri Begawan</p>
                    </div>
                </div>
            </div>

            <button type="button"
                    @click="activeModal = null"
                    class="block w-full text-center rounded-sm py-2.5 text-sm font-semibold tracking-wide uppercase text-white transition-colors
                           bg-[#0F1B2D] hover:bg-[#1B2D47]">
                Close
            </button>
        </div>

        {{-- FORGOT PASSWORD MODAL --}}
        <div x-show="activeModal === 'forgot'"
             x-transition
             @click.outside="activeModal = null"
             class="relative w-full max-w-sm rounded-sm p-8 bg-[#F5F3EE]">

            <button type="button"
                    @click="activeModal = null"
                    class="absolute top-4 right-4 text-slate-400 hover:text-slate-700">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>

            <div class="flex justify-center mb-5">
                <div class="w-12 h-12 rounded-full bg-[#0F1B2D] flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-[#C9A227]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25a3 3 0 013 3m3 0a6 6 0 01-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1121.75 8.25z" />
                    </svg>
                </div>
            </div>

            <h2 class="text-center text-sm font-semibold tracking-wide uppercase text-slate-800 mb-2">
                Password Reset
            </h2>

            <p class="text-center text-sm text-slate-600 leading-relaxed mb-6">
                Password resets on NOVA are handled by your administrator.
                Please reach out with your registered email to request a reset.
            </p>

            <div class="rounded-sm bg-white ring-1 ring-slate-300 px-4 py-4 space-y-3 mb-6">
                <div class="flex items-start gap-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mt-0.5 text-[#0F1B2D] shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                    </svg>
                    <div>
                        <p class="text-[11px] font-semibold tracking-wide uppercase text-slate-500">Email</p>
                        <a href="mailto:admin@nova.mindef.gov.bn" class="text-sm text-slate-800 hover:text-[#0F1B2D]">
                            admin@nova.mindef.gov.bn
                        </a>
                    </div>
                </div>

                <div class="flex items-start gap-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mt-0.5 text-[#0F1B2D] shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a1.5 1.5 0 001.5-1.5v-3.028a1.5 1.5 0 00-1.212-1.472l-4.493-.899a1.5 1.5 0 00-1.541.67l-.622 1.036a12.032 12.032 0 01-6.16-6.16l1.036-.622a1.5 1.5 0 00.67-1.541l-.899-4.493A1.5 1.5 0 007.028 3H4.5a1.5 1.5 0 00-1.5 1.5v2.25z" />
                    </svg>
                    <div>
                        <p class="text-[11px] font-semibold tracking-wide uppercase text-slate-500">Phone</p>
                        <a href="tel:+6732000000" class="text-sm text-slate-800 hover:text-[#0F1B2D]">
                            +673 2XX XXXX
                        </a>
                    </div>
                </div>

                <div class="flex items-start gap-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mt-0.5 text-[#0F1B2D] shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 6.75V15m6-6v8.25m.503 3.498l4.875-2.437c.381-.19.622-.58.622-1.006V4.82c0-.836-.88-1.38-1.628-1.006l-3.869 1.934c-.317.159-.69.159-1.006 0L9.503 3.804a1.125 1.125 0 00-1.006 0L3.622 6.24C3.24 6.43 3 6.822 3 7.247v10.006c0 .836.88 1.38 1.628 1.006l3.869-1.934c.317-.159.69-.159 1.006 0l4.994 2.497c.317.158.69.158 1.006 0z" />
                    </svg>
                    <div>
                        <p class="text-[11px] font-semibold tracking-wide uppercase text-slate-500">Office</p>
                        <p class="text-sm text-slate-800">MINDEF, Bandar Seri Begawan</p>
                    </div>
                </div>
            </div>

            <button type="button"
                    @click="activeModal = null"
                    class="block w-full text-center rounded-sm py-2.5 text-sm font-semibold tracking-wide uppercase text-white transition-colors
                           bg-[#0F1B2D] hover:bg-[#1B2D47]">
                Close
            </button>
        </div>

    </div>

</body>
</html>