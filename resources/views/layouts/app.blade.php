@php
use App\Models\Slot;
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-BmbxuPwQa2lc/FVzBcNJ7UAyJxM6wuqIj61tLrc4wSX0szH/Ev+nYRRuWlolflfl" crossorigin="anonymous">
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
                    <li class="nav-item">
                        @if ($name=='dashboard' )
                        <a class="nav-link active" aria-current="page" href="{{ route('dashboard') }}">Dashboard</a>
                        @else
                        <a class="nav-link" href="{{ route('dashboard') }}">Dashboard</a>
                        @endif
                    </li>
                    <li class="nav-item">
                        @if ($name=='admin.users' )
                        <a class="nav-link active" aria-current="page" href="{{ route('admin.users') }}">Users</a>
                        @else
                        <a class="nav-link" href="{{ route('admin.users') }}">Users</a>
                        @endif
                    </li>
                    <li class="nav-item">
                        @if ($name=='admin.probations' )
                        <a class="nav-link active" aria-current="page" href="{{ route('admin.probations') }}">Probations</a>
                        @else
                        <a class="nav-link" href="{{ route('admin.probations') }}">Probations</a>
                        @endif
                    </li>
                    <li class="nav-item">
                        @if ($name=='admin.reports' )
                        <a class="nav-link active" aria-current="page" href="{{ route('admin.reports') }}">Reports</a>
                        @else
                        <a class="nav-link" href="{{ route('admin.reports') }}">Reports</a>
                        @endif
                    </li>
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
    <button type="button" class="btn btn-dark rounded-circle position-fixed bottom-0 end-0 mb-3 me-3 shadow" data-bs-toggle="modal" data-bs-target="#reportModal" style="width:50px;height:50px;"><i data-feather="alert-circle"></i></button>

    <div class="modal fade" id="reportModal" tabindex="-1" aria-labelledby="reportModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="reportModalLabel">Submit a report</h5>
                </div>
                <div class="modal-body">
                    <form class="needs-validation ajax" id="reportForm" novalidate>
                        <div class="input-group mb-3" id="typeReport">
                            <span class="input-group-text">Type</span>
                            <select class="form-select" name="type">
                                <option value="1">Technical problem/bug report</option>
                                @if (User::find(Auth::user()->id)->role() == 'tutor' && Slot::where('tutor_id', Auth::user()->id)->whereBetween('start', [date('Y-m-d 00:00:00', strtotime('-1 day')), date('Y-m-d 23:59:59', strtotime('+1 day'))])->exists())
                                <option value="2">Absent student report</option>
                                @elseif (User::find(Auth::user()->id)->role() == 'student' && Slot::where('student_id', Auth::user()->id)->whereBetween('start', [date('Y-m-d 00:00:00', strtotime('-1 day')), date('Y-m-d 23:59:59', strtotime('+1 day'))])->exists())
                                <option value="2">Absent tutor report</option>
                                @endif
                            </select>
                        </div>
                        <div class="form-floating" id="messageReport">
                            <textarea class="form-control" maxlength="1000" name="message" required></textarea>
                            <label>Briefly describe the report</label>
                            <small class="form-text text-muted">
                                Limit 1000 characters.
                            </small>
                        </div>
                        <div class="form-floating" id="startReport">
                            <select class="form-select" name="start">
                            </select>
                            <label>Which slot today were the person missing?</label>
                            <small class="form-text text-muted">
                                Only report if the person wasn't there more than 10 minutes after the session starts.
                            </small>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="reportBtn">Submit</button>
                </div>
            </div>
        </div>
    </div>
    <!-- scripts -->
    <script src="https://cdn.jsdelivr.net/npm/feather-icons@4.28.0/dist/feather.min.js" integrity="sha256-XfzdiC+S1keia+s9l07y7ye5a874sBq67zK4u7LTjvk=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/js/bootstrap.bundle.min.js" integrity="sha384-b5kHyXgcpbZJO/tY9Ul7kGkf1S0CWuKcCD38l8YkeH8z8QjE0GmW1gYU5S9FOnJ0" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/luxon@1.25.0/build/global/luxon.min.js" integrity="sha256-OVk2fwTRcXYlVFxr/ECXsakqelJbOg5WCj1dXSIb+nU=" crossorigin="anonymous"></script>
    <script>
        var layout = true;
    </script>
    <script src="{{ asset('js/main.js') }}"></script>
    @yield('scripts')
</body>
<footer class="mt-4 pb-3">
    <p class="text-center text-muted">© <span id="year"></span> Tutoring for All<br>Designed by Dennis Eum</p>
</footer>

</html>