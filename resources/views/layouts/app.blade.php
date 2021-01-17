@php
use App\Models\User;
use App\Models\Subject;
use Illuminate\Support\Facades\Route;

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
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('images/favicon-16x16.png') }}">

    <title>{{ config('app.name', 'tfa-calendar') }}</title>

    <!-- css -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@5.4.0/main.min.css">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('css/main.css') }}" rel="stylesheet">
</head>

<body>
    <nav class="navbar navbar-expand-md navbar-light fixed-top bg-white shadow-sm">
        <a class="navbar-brand bg-secondary text-light px-2 border-clean" href="{{ url('/dashboard') }}">
            {{ config('app.name', 'tfa-calendar') }}
        </a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse mt-2 mt-md-0" id="navbarSupportedContent">
            @if (User::find(Auth::user()->id)->role() == 'student' && $name != 'settings')
            <ul class="navbar-nav mr-auto my-3 my-md-0">
                <div class="row" style="width:500px;">
                    <div class="col input-group">
                        <div class="input-group-prepend">
                            <label class="input-group-text">Subjects</label>
                        </div>
                        <select class="custom-select" id="calendarSubjects">
                            <option value="0" selected>General</option>
                            @foreach(Subject::get() as $item)
                            <option value="{{ $item->id }}">{{ $item->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-4" id="spinnerArea" style="padding-top:3px;">
                    </div>
                </div>
            </ul>
            @endif
            @if ($name == 'settings')
            <ul class="navbar-nav mr-auto my-3 my-md-0">
                <a class="btn btn-primary" href="{{ route('dashboard') }}"><i data-feather="chevron-left"></i><span class="align-middle">Back to dashboard</span></a>
            </ul>
            @endif
            <ul class="navbar-nav ml-auto">
                @if ($name != 'settings')
                <li class="nav-item dropdown">
                    <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                        {{ Auth::user()->name }}
                    </a>
                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                        <a class="dropdown-item" href="{{ route('settings') }}">
                            {{ __('Settings') }}
                        </a>
                        <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                            {{ __('Logout') }}
                        </a>
                    </div>
                </li>
                @else
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('logout') }}" onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                        {{ __('Logout') }}
                    </a>
                </li>
                @endif
                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                    @csrf
                </form>
            </ul>
        </div>
    </nav>

    @yield('content')

    <!-- scripts -->
    <script src="https://cdn.jsdelivr.net/npm/feather-icons@4.28.0/dist/feather.min.js" integrity="sha256-XfzdiC+S1keia+s9l07y7ye5a874sBq67zK4u7LTjvk=" crossorigin="anonymous"></script>
    <script src="{{ asset('js/app.js') }}"></script>
    <script src="{{ asset('js/main.js') }}"></script>
    @yield('scripts')
</body>
<footer class="mt-4 pb-3">
    <p class="text-center text-muted">© <span id="year"></span> Dennis Eum. Tutoring For All.</p>
</footer>

</html>