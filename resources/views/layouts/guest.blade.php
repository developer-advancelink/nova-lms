{{--
    GUEST LAYOUT — NOVA (navy theme)

    The shell for signed-out / pre-authentication screens rendered through
    <x-guest-layout>, i.e. app/View/Components/GuestLayout.php. Matches the
    hand-rolled shell in auth/login.blade.php, auth/forgot-password.blade.php
    and auth/register.blade.php: navy page, NOVA wordmark, cream card, and the
    standalone footer partial that needs no authenticated user.

    Props:
      $title  optional; browser tab title, suffixed with " — NOVA"
--}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ? $title . ' — NOVA' : 'NOVA' }}</title>
    @vite('resources/css/app.css')
</head>

<body class="min-h-screen flex flex-col overflow-y-auto bg-[#0F1B2D]">

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

                {{-- VALIDATION ERRORS --}}
                {{-- Session status is left to each page: Laravel's status values here
                     are machine-readable sentinels ('verification-link-sent'), not
                     prose, so printing them generically would leak a slug to the user. --}}
                @if($errors->any())
                    <div class="mb-5 rounded-sm bg-rose-50 ring-1 ring-rose-300 px-4 py-3 text-sm text-rose-800">
                        <ul class="space-y-1">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{ $slot }}

            </div>

        </div>
    </div>

    {{-- FOOTER --}}
    @include('partials.login-footer')

</body>
</html>
