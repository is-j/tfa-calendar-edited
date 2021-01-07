<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="author" content="Dennis Eum">
    <meta name="csrf-token" content="{{ csrf_token() }}">

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
    <script src="{{ asset('js/app.js') }}"></script>
    <script src="{{ asset('js/main.js') }}"></script>
    @yield('scripts')
</body>
<footer class="fixed-bottom pb-3">
    <p class="text-center text-muted">tfa © <span id="year"></span>, dennis eum</p>
</footer>

</html>