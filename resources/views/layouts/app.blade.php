@php use Illuminate\Support\Facades\Auth; @endphp

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'NOVA')</title>
    @vite('resources/css/app.css')

    {{-- Hide body until styles are ready --}}
    <style>
        body { visibility: hidden; }

        #nova-loader {
            visibility: visible;
            position: fixed;
            inset: 0;
            background: #0F1B2D;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 99999;
            opacity: 1;
            transition: opacity 0.3s ease;
        }

        #nova-loader .wordmark {
            text-align: center;
        }

        #nova-loader .title {
            color: white;
            font-family: sans-serif;
            font-size: 1.25rem;
            font-weight: 600;
            letter-spacing: 0.15em;
        }

        #nova-loader .subtitle {
            color: #7C8AA0;
            font-family: sans-serif;
            font-size: 0.6rem;
            letter-spacing: 0.18em;
            text-transform: uppercase;
            margin-top: 6px;
        }

        #nova-loader .dots {
            margin-top: 20px;
            display: flex;
            justify-content: center;
            gap: 6px;
        }

        #nova-loader .dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: #C9A227;
            animation: nova-pulse 1.2s ease-in-out infinite;
        }

        #nova-loader .dot:nth-child(2) { animation-delay: 0.2s; }
        #nova-loader .dot:nth-child(3) { animation-delay: 0.4s; }

        @keyframes nova-pulse {
            0%, 100% { opacity: 0.2; transform: scale(0.8); }
            50%       { opacity: 1;   transform: scale(1.2); }
        }

        [x-cloak] { display: none !important; }
    </style>
</head>

<body class="min-h-screen font-sans bg-[#E8EEF4] text-slate-900">

    {{-- NOVA LOADING SCREEN --}}
    <div id="nova-loader">
        <div class="wordmark">
            <p class="title">NOVA</p>
            <p class="subtitle">Networked Online Virtual Academy</p>
            <div class="dots">
                <span class="dot"></span>
                <span class="dot"></span>
                <span class="dot"></span>
            </div>
        </div>
    </div>

    {{-- SIDEBAR --}}
    @if(Auth::check() && Auth::user()->role === 'lecturer')
        @include('partials.sidebar', [
            'role' => 'lecturer',
            'basePath' => '/lecturer',
            'courses' => $courses ?? collect(),
        ])
    @else
        @include('partials.sidebar', [
            'role' => 'student',
            'basePath' => '',
            'courses' => $enrolledCourses ?? collect(),
        ])
    @endif

    {{-- MAIN WRAPPER --}}
    <div class="ml-64 min-h-screen flex flex-col">

        {{-- TOPBAR --}}
        @if(Auth::check() && Auth::user()->role === 'lecturer')
            @include('partials.topbar', [
                'role' => 'lecturer',
                'quickActions' => [
                    ['label' => 'New assignment', 'href' => '/lecturer/courses'],
                    ['label' => 'Post announcement', 'href' => '/lecturer/courses'],
                    ['label' => 'New course', 'href' => '/lecturer/courses'],
                ],
            ])
        @else
            @include('partials.topbar', ['role' => 'student'])
        @endif

        {{-- MAIN CONTENT --}}
        <div class="flex-1 px-8 py-6">
            @include('partials.main')
        </div>

        {{-- FOOTER --}}
        @include('partials.footer')

    </div>

    {{-- Alpine.js --}}
    <script src="//unpkg.com/alpinejs" defer></script>

    {{-- Hide loader only after ALL resources (including Vite CSS) are fully loaded --}}
    {{-- Wait until Tailwind CSS is actually applied before revealing the page --}}
    <script>
    // Keep loader visible for minimum 800ms regardless of load timing
    // This covers Vite dev mode CSS injection delay
    var loaderStart = Date.now();
    var minDisplay = 800;

    function hideLoader() {
        var elapsed = Date.now() - loaderStart;
        var remaining = Math.max(0, minDisplay - elapsed);
        setTimeout(function () {
            document.body.style.visibility = 'visible';
            var loader = document.getElementById('nova-loader');
            loader.style.opacity = '0';
            setTimeout(function () { loader.style.display = 'none'; }, 300);
        }, remaining);
    }

    window.addEventListener('load', hideLoader);
</script>

</body>
</html>