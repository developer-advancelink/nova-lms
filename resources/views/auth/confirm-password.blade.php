<x-guest-layout title="Confirm Password">

    {{-- ICON --}}
    <div class="flex justify-center mb-5">
        <div class="w-12 h-12 rounded-full bg-[#0F1B2D] flex items-center justify-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-[#C9A227]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
            </svg>
        </div>
    </div>

    {{-- HEADING --}}
    <h2 class="text-center text-sm font-semibold tracking-wide uppercase text-slate-800 mb-2">
        Confirm Password
    </h2>

    {{-- MESSAGE --}}
    <p class="text-center text-sm text-slate-600 leading-relaxed mb-6">
        This is a secure area of NOVA. Please confirm your password before continuing.
    </p>

    {{-- FORM --}}
    <form method="POST" action="{{ route('password.confirm') }}" class="space-y-5">

        @csrf

        {{-- PASSWORD --}}
        <div>
            <label for="password" class="block text-[11px] font-semibold tracking-wide uppercase text-slate-600 mb-1.5">
                Password
            </label>
            <input type="password"
                   name="password"
                   id="password"
                   required
                   autofocus
                   autocomplete="current-password"
                   class="w-full rounded-sm bg-white ring-1 ring-slate-300 px-3 py-2.5 text-sm text-slate-900
                          outline-none focus:ring-2 focus:ring-[#0F1B2D] transition-all">
        </div>

        {{-- SUBMIT --}}
        <button type="submit"
                class="w-full rounded-sm py-2.5 text-sm font-semibold tracking-wide uppercase text-white transition-colors
                       bg-[#0F1B2D] hover:bg-[#1B2D47]">
            Confirm
        </button>

    </form>

</x-guest-layout>
