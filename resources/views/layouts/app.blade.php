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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-giJF6kkoqNQ00vy+HMDP7azOuL0xtbfIcaT9wjKHr8RbDVddVHyTfAAsrekwKmP1" crossorigin="anonymous">
    <link href="{{ asset('css/main.css') }}" rel="stylesheet">
</head>

<body>
    <nav class="navbar navbar-expand-md navbar-light bg-white fixed-top shadow-sm">
        <div class="container-fluid">
            <a class="navbar-brand style-brand text-dark shadow-sm px-2" href="{{ url('/dashboard') }}">{{ config('app.name', 'tfa-calendar') }}</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav me-auto my-2 my-md-0">
                    @if (User::find(Auth::user()->id)->role() == 'student' && $name != 'settings')
                    <div class="row" style="width:500px;">
                        <div class="col input-group">
                            <div class="input-group-prepend">
                                <label class="input-group-text">Subjects</label>
                            </div>
                            <select class="form-select" id="calendarSubjects">
                                <option value="0" selected>General</option>
                                @foreach(Subject::get() as $item)
                                <option value="{{ $item->id }}">{{ $item->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-4" id="spinnerArea" style="padding-top:3px;">
                        </div>
                    </div>
                    @elseif ($name == 'settings')
                    <a class="btn btn-primary" href="{{ route('dashboard') }}">Back to dashboard</a>
                    @elseif (User::find(Auth::user()->id)->role() == 'admin')
                    
                    @endif
                </ul>
                <ul class="navbar-nav ms-auto my-2 my-md-0">
                    @if ($name != 'settings')
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            {{ Auth::user()->name }}
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                            <a class="dropdown-item" href="{{ route('settings') }}">
                                {{ __('Settings') }}
                            </a>
                            <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                {{ __('Logout') }}
                            </a>
                        </ul>
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
        </div>
    </nav>
    @yield('content')

    <!-- scripts -->
    <script src="https://cdn.jsdelivr.net/npm/feather-icons@4.28.0/dist/feather.min.js" integrity="sha256-XfzdiC+S1keia+s9l07y7ye5a874sBq67zK4u7LTjvk=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/js/bootstrap.bundle.min.js" integrity="sha384-ygbV9kiqUc6oa4msXn9868pTtWMgiQaeYH7/t7LECLbyPA2x65Kgf80OJFdroafW" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
    <script src="{{ asset('js/main.js') }}"></script>
    @yield('scripts')
</body>
<footer class="mt-4 pb-3">
    <p class="text-center text-muted">© <span id="year"></span> Dennis Eum. Tutoring for All.</p>
</footer>

</html>