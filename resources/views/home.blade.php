<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    {{-- @yield lets child views inject their own <title> --}}
    <title>@yield('title', 'Shorty — URL Shortener')</title>

    {{-- CSRF meta tag --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- Google Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Mono:wght@400;700&family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet">

    {{-- Our stylesheet --}}
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">

    {{-- Child views can add their own <head> content here --}}
    @stack('head')
</head>
<body>

    {{-- ── NAVIGATION ── --}}
    <nav class="nav">
        <div class="container nav__inner">
            <a href="{{ url('/') }}" class="nav__logo">Shorty</a>
            <span style="font-size:0.78rem; color:var(--muted);">Link smarter.</span>
        </div>
    </nav>

    {{-- ── PAGE CONTENT ── --}}
    <main>
        @yield('content')
    </main>

    {{-- ── FOOTER ── --}}
    <footer>
        <div class="container">
            &copy; {{ date('Y') }} Shorty &mdash;
            <a href="/cookie-policy">Cookie Policy</a> &middot;
            <a href="/privacy">Privacy</a>
        </div>
    </footer>

    {{-- ── COOKIE BANNER ── --}}
    {{--
        WHY HERE (end of body)?
        Putting it before </body> means the DOM is ready by the time
        our script runs. It also doesn't block rendering of page content.
    --}}
    @include('partials.cookie-banner')

    {{-- ── SCRIPTS ── --}}
    {{--
        asset() generates the correct public URL:
        e.g. http://localhost/js/cookie-consent.js
    --}}
    <script src="{{ asset('js/cookie-consent.js') }}"></script>

    {{-- Child views can push extra scripts here --}}
    @stack('scripts')

</body>
</html>
