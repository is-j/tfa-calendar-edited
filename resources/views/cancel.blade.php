@extends('layouts.auth')

@section('content')
<div class="card form-accounts mx-3 shadow">
    <div class="card-body">
        <h5 class="card-title">
            <div class="d-inline-block bg-secondary text-light p-2 border-clean">tfa-calendar</div>&nbsp;&nbsp;{{ __('Login') }}
        </h5>
        @if (User::find(Auth::user()->id)->role() == 'tutor')
        <form class="needs-validation ajax" id="cancelSlotForm" novalidate>
            <div class="input-group mb-3" id="startCancel">
                <div class="input-group-prepend">
                    <span class="input-group-text">Date/Time</span>
                </div>
                <input type="datetime-local" class="form-control" value="{{ app('request')->input('start') }}" name="start" disabled>
            </div>
            <div class="input-group mb-3" id="studentnameCancel">
                <div class="input-group-prepend">
                    <span class="input-group-text">Student name</span>
                </div>
                <input type="text" class="form-control" value="{{ app('request')->input('studentname') }}" name="studentname" disabled>
            </div>
            <div class="input-group mb-3" id="studentemailCancel">
                <div class="input-group-prepend">
                    <span class="input-group-text">Student email</span>
                </div>
                <input type="text" class="form-control" value="{{ app('request')->input('studentemail') }}" name="studentemail" disabled>
            </div>
            <div class="input-group mb-3" id="subjectCancel">
                <div class="input-group-prepend"> <span class="input-group-text">Subject</span> </div><input type="text" class="form-control" value="{{ app('request')->input('subject') }}" name="subject" disabled>
            </div>
            <div class="input-group mb-3" id="infoCancel">
                <div class="input-group-prepend">
                    <span class="input-group-text">Topic needs</span>
                </div>
                <textarea class="form-control" maxlength="1000" rows="3" name="info" disabled required>{{ app('request')->input('info') }}</textarea>
            </div>
            <div class="input-group" id="reasonCancel">
                <div class="input-group-prepend">
                    <span class="input-group-text">Cancellation reason</span>
                </div>
                <textarea class="form-control" maxlength="1000" rows="3" name="reason" disabled required></textarea>
            </div>
        </form>
        @elseif (User::find(Auth::user()->id)->role() == 'student')
        <form class="needs-validation ajax" id="cancelSlotForm" novalidate>
            <div class="input-group mb-3" id="startCancel">
                <div class="input-group-prepend">
                    <span class="input-group-text">Date/Time</span>
                </div>
                <input type="datetime-local" class="form-control" value="{{ app('request')->input('start') }}" name="start" disabled>
            </div>
            <div class="input-group mb-3" id="tutornameCancel">
                <div class="input-group-prepend">
                    <span class="input-group-text">Tutor name</span>
                </div>
                <input type="text" class="form-control" value="{{ app('request')->input('tutorname') }}" name="tutorname" disabled>
            </div>
            <div class="input-group mb-3" id="tutoremailCancel">
                <div class="input-group-prepend">
                    <span class="input-group-text">Tutor email</span>
                </div>
                <input type="text" class="form-control" value="{{ app('request')->input('tutoremail') }}" name="tutoremail" disabled>
            </div>
            <div class="input-group mb-3" id="subjectCancel">
                <div class="input-group-prepend"> <span class="input-group-text">Subject</span> </div><input type="text" class="form-control" value="{{ app('request')->input('subject') }}" name="subject" disabled>
            </div>
            <div class="input-group mb-3" id="infoCancel">
                <div class="input-group-prepend">
                    <span class="input-group-text">Topic needs</span>
                </div>
                <textarea class="form-control" maxlength="1000" rows="3" name="info" disabled required>{{ app('request')->input('info') }}</textarea>
            </div>
            <div class="input-group" id="reasonCancel">
                <div class="input-group-prepend">
                    <span class="input-group-text">Cancellation reason</span>
                </div>
                <textarea class="form-control" maxlength="1000" rows="3" name="reason" required></textarea>
            </div>
        </form>
        @endif
        <form class="needs-validation text-left" method="POST" action="{{ route('login') }}" novalidate>
            @csrf
            <div class="form-group">
                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" placeholder="Email" required autocomplete="email" autofocus>
                @error('email')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>
            <div class="form-group">
                <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="Password" required autocomplete="current-password">
                @error('password')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>
            <button type="submit" class="btn btn-primary btn-block">
                {{ __('Login') }}
            </button>
            <div class="text-center">
                <p class="mt-3 mb-0">Don't have an account? <a href="{{ route('register') }}">Register here.</a></p>
            </div>
        </form>
    </div>
</div>
@endsection