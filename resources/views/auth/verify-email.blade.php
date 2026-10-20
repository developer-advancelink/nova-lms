<x-guest-layout title="Verify Email">

    {{-- ICON --}}
    <div class="flex justify-center mb-5">
        <div class="w-12 h-12 rounded-full bg-[#0F1B2D] flex items-center justify-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-[#C9A227]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
            </svg>
        </div>
    </div>

    {{-- HEADING --}}
    <h2 class="text-center text-sm font-semibold tracking-wide uppercase text-slate-800 mb-2">
        Verify Your Email
    </h2>

    {{-- MESSAGE --}}
    <p class="text-center text-sm text-slate-600 leading-relaxed mb-6">
        Before getting started, please confirm your email address by clicking the
        link we just sent you. If it never arrived, we will gladly send another.
    </p>

    {{-- RESEND CONFIRMATION --}}
    @if(session('status') === 'verification-link-sent')
        <div class="mb-6 rounded-sm bg-emerald-50 ring-1 ring-emerald-300 px-4 py-3 text-sm text-emerald-800">
            A new verification link has been sent to the email address you provided during registration.
        </div>
    @endif

    {{-- RESEND --}}
    <form method="POST" action="{{ route('verification.send') }}">
        @csrf
        <button type="submit"
                class="w-full rounded-sm py-2.5 text-sm font-semibold tracking-wide uppercase text-white transition-colors
                       bg-[#0F1B2D] hover:bg-[#1B2D47]">
            Resend verification email
        </button>
    </form>

    {{-- LOG OUT --}}
    <form method="POST" action="{{ route('logout') }}" class="mt-4">
        @csrf
        <button type="submit"
                class="w-full text-center text-xs font-medium text-slate-600 hover:text-slate-900 underline underline-offset-2">
            Log out
        </button>
    </form>

</x-guest-layout>
