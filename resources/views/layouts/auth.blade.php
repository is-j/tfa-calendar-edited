<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="author" content="Dennis Eum">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('images/favicon-16x16.png') }}">

    <title>tfa-calendar</title>

    @yield('styles')
    <link href="{{ mix('css/app.css') }}" rel="stylesheet">
</head>

<body class="bg-gray-100">
    <div class="flex flex-col h-screen justify-between">
        <main class="h-full">
            <div class="container-auth text-center">
                <div class="w-full md:w-96 rounded-md shadow-lg py-1 bg-white ring-1 ring-black py-3 px-4 ring-opacity-5">
                    @yield('content')
                </div>
            </div>
        </main>
        <footer class="mt-4 pb-3">
            <p class="text-center text-gray-400">© <span id="year"></span> Dennis Eum, TFA</p>
        </footer>
    </div>
    <script>
        const reportEnabled = false;
    </script>
    <script src="https://cdn.jsdelivr.net/npm/luxon@1.26.0/build/global/luxon.min.js" integrity="sha256-4sbTzmCCW9LGrIh5OsN8V5Pfdad1F1MwhLAOyXKnsE0=" crossorigin="anonymous"></script>
    <script src="{{ asset('js/config.js') }}"></script>
    <script src="{{ mix('js/app.js') }}"></script>
    @yield('scripts')
</body>

</html>