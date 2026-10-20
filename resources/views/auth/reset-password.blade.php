<x-guest-layout title="Reset Password">

    {{-- ICON --}}
    <div class="flex justify-center mb-5">
        <div class="w-12 h-12 rounded-full bg-[#0F1B2D] flex items-center justify-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-[#C9A227]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25a3 3 0 013 3m3 0a6 6 0 01-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1121.75 8.25z" />
            </svg>
        </div>
    </div>

    {{-- HEADING --}}
    <h2 class="text-center text-sm font-semibold tracking-wide uppercase text-slate-800 mb-6">
        Reset Password
    </h2>

    {{-- FORM --}}
    <form method="POST" action="{{ route('password.store') }}" class="space-y-5">

        @csrf

        {{-- RESET TOKEN --}}
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        {{-- EMAIL --}}
        <div>
            <label for="email" class="block text-[11px] font-semibold tracking-wide uppercase text-slate-600 mb-1.5">
                Email address
            </label>
            <input type="email"
                   name="email"
                   id="email"
                   value="{{ old('email', $request->email) }}"
                   required
                   autofocus
                   autocomplete="username"
                   class="w-full rounded-sm bg-white ring-1 ring-slate-300 px-3 py-2.5 text-sm text-slate-900
                          outline-none focus:ring-2 focus:ring-[#0F1B2D] transition-all">
        </div>

        {{-- NEW PASSWORD --}}
        <div>
            <label for="password" class="block text-[11px] font-semibold tracking-wide uppercase text-slate-600 mb-1.5">
                New password
            </label>
            <input type="password"
                   name="password"
                   id="password"
                   required
                   autocomplete="new-password"
                   class="w-full rounded-sm bg-white ring-1 ring-slate-300 px-3 py-2.5 text-sm text-slate-900
                          outline-none focus:ring-2 focus:ring-[#0F1B2D] transition-all">
        </div>

        {{-- CONFIRM PASSWORD --}}
        <div>
            <label for="password_confirmation" class="block text-[11px] font-semibold tracking-wide uppercase text-slate-600 mb-1.5">
                Confirm new password
            </label>
            <input type="password"
                   name="password_confirmation"
                   id="password_confirmation"
                   required
                   autocomplete="new-password"
                   class="w-full rounded-sm bg-white ring-1 ring-slate-300 px-3 py-2.5 text-sm text-slate-900
                          outline-none focus:ring-2 focus:ring-[#0F1B2D] transition-all">
        </div>

        {{-- SUBMIT --}}
        <button type="submit"
                class="w-full rounded-sm py-2.5 text-sm font-semibold tracking-wide uppercase text-white transition-colors
                       bg-[#0F1B2D] hover:bg-[#1B2D47]">
            Reset password
        </button>

    </form>

    {{-- BACK TO LOGIN --}}
    <p class="text-center text-xs text-slate-500 mt-6">
        <a href="{{ route('login') }}" class="font-medium text-slate-600 hover:text-slate-900 underline underline-offset-2">
            Back to sign in
        </a>
    </p>

</x-guest-layout>
