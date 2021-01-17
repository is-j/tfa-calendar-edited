@php
$name = Route::currentRouteName();
@endphp
<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="author" content="Dennis Eum">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- favicon -->
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">

    <title>{{ config('app.name', 'tfa-calendar') }}</title>

    <!-- css -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('css/main.css') }}" rel="stylesheet">
    <link href="{{ asset('css/auth.css') }}" rel="stylesheet">
</head>

<body class="text-center bg-light">
    <!-- content -->
    @yield('content')

    <!-- scripts -->
    <script src="https://cdn.jsdelivr.net/npm/feather-icons@4.28.0/dist/feather.min.js" integrity="sha256-XfzdiC+S1keia+s9l07y7ye5a874sBq67zK4u7LTjvk=" crossorigin="anonymous"></script>
    <script src="{{ asset('js/app.js') }}"></script>
    <script src="{{ asset('js/main.js') }}"></script>
    @yield('scripts')
</body>
@if ($name != 'cancel')
<footer class="fixed-bottom pb-3">
    <p class="text-center text-muted">Copyright © <span id="year"></span>Tutoring For All. Designed by Dennis Eum.</p>
</footer>
@endif
</html>