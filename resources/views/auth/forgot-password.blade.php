<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password — NOVA</title>
    @vite('resources/css/app.css')
</head>

<body class="min-h-screen flex flex-col overflow-y-auto bg-[#0F1B2D]">

    {{-- FORGOT PASSWORD CONTENT --}}
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

                {{-- ICON --}}
                <div class="flex justify-center mb-5">
                    <div class="w-12 h-12 rounded-full bg-[#0F1B2D] flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-[#C9A227]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25a3 3 0 013 3m3 0a6 6 0 01-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1121.75 8.25z" />
                        </svg>
                    </div>
                </div>

                {{-- HEADING --}}
                <h2 class="text-center text-sm font-semibold tracking-wide uppercase text-slate-800 mb-2">
                    Password Reset
                </h2>

                {{-- MESSAGE --}}
                <p class="text-center text-sm text-slate-600 leading-relaxed mb-6">
                    Password resets on NOVA are handled by your administrator.
                    Please reach out with your registered email to request a reset.
                </p>

                {{-- CONTACT DETAILS --}}
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
                            <p class="text-sm text-slate-800">
                                MINDEF, Bandar Seri Begawan
                            </p>
                        </div>
                    </div>

                </div>

                {{-- BACK TO LOGIN --}}
                <a href="{{ route('login') }}"
                   class="block w-full text-center rounded-sm py-2.5 text-sm font-semibold tracking-wide uppercase text-white transition-colors
                          bg-[#0F1B2D] hover:bg-[#1B2D47]">
                    Back to sign in
                </a>

            </div>

        </div>
    </div>

    {{-- FOOTER --}}
    @include('partials.login-footer')

</body>
</html>