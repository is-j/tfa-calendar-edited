@php
use App\Models\User;
@endphp
@extends('layouts.auth')

@section('content')
<div class="card form-accounts mx-3 shadow">
    <div class="card-body">
        <h5 class="card-title">
            <div class="d-inline-block bg-secondary text-light p-2 border-clean">tfa-calendar</div>&nbsp;&nbsp;{{ __('Login') }}
        </h5>
        @if (User::find(Auth::user()->id)->role() == 'tutor')
        <form class="needs-validation ajax" id="cancelSlotForm" novalidate>
            <div class="form-floating mb-3" id="startCancel">
                <input type="datetime-local" class="form-control" value="{{ app('request')->input('start') }}" name="start" disabled>
                <label>Date/Time</label>
            </div>
            <div class="form-floating mb-3" id="studentnameCancel">
                <input type="text" class="form-control" value="{{ app('request')->input('studentname') }}" name="name" disabled>
                <label>Student name</label>
            </div>
            <div class="form-floating mb-3" id="studentemailCancel">
                <input type="text" class="form-control" value="{{ app('request')->input('studentemail') }}" name="email" disabled>
                <label>Student email</label>
            </div>
            <div class="form-floating mb-3" id="subjectCancel">
                <input type="text" class="form-control" value="{{ app('request')->input('subject') }}" name="subject" disabled>
                <label>Subject</label>
            </div>
            <div class="form-floating mb-3" id="infoCancel">
                <textarea class="form-control" maxlength="1000" name="info" disabled required>{{ app('request')->input('info') }}</textarea>
                <label>Topic needs</label>
            </div>
            <div class="form-floating mb-3" id="reasonCancel">
                <textarea class="form-control" maxlength="1000" rows="3" name="reason" required></textarea>
                <label>Cancellation reason</label>
                <div class="invalid-feedback text-left">
                    You must provide a reason.
                </div>
            </div>
            <div class="row">
                <div class="col-md d-grid mb-3 mb-md-0">
                    <button type="submit" class="btn btn-danger">Cancel slot</button>
                </div>
                <div class="col-md d-grid">
                    <a class="btn btn-primary" href="{{ route('dashboard') }}">Back to dashboard</a>
                </div>
            </div>
        </form>
        @elseif (User::find(Auth::user()->id)->role() == 'student')
        <form class="needs-validation ajax" id="cancelSlotForm" novalidate>
            <div class="form-floating mb-3" id="startCancel">
                <input type="datetime-local" class="form-control" value="{{ app('request')->input('start') }}" name="start" disabled>
                <label>Date/Time</label>
            </div>
            <div class="form-floating mb-3" id="tutornameCancel">
                <input type="text" class="form-control" value="{{ app('request')->input('tutorname') }}" name="name" disabled>
                <label>Tutor name</label>
            </div>
            <div class="form-floating mb-3" id="tutoremailCancel">
                <input type="text" class="form-control" value="{{ app('request')->input('tutoremail') }}" name="email" disabled>
                <label>Tutor email</label>
            </div>
            <div class="form-floating mb-3" id="subjectCancel">
                <input type="text" class="form-control" value="{{ app('request')->input('subject') }}" name="subject" disabled>
                <label>Subject</label>
            </div>
            <div class="form-floating mb-3" id="infoCancel">
                <textarea class="form-control" maxlength="1000" name="info" disabled required>{{ app('request')->input('info') }}</textarea>
                <label>Topic needs</label>
            </div>
            <div class="form-floating mb-3" id="reasonCancel">
                <textarea class="form-control" maxlength="1000" rows="3" name="reason" required></textarea>
                <label>Cancellation reason</label>
                <div class="invalid-feedback text-left">
                    You must provide a reason.
                </div>
            </div>
            <div class="row">
                <div class="col-md d-grid mb-3 mb-md-0">
                    <button type="submit" class="btn btn-danger">Cancel slot</button>
                </div>
                <div class="col-md d-grid">
                    <a class="btn btn-primary" href="{{ route('dashboard') }}">Back to dashboard</a>
                </div>
            </div>
        </form>
        @endif
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/luxon@1.25.0/build/global/luxon.min.js" integrity="sha256-OVk2fwTRcXYlVFxr/ECXsakqelJbOg5WCj1dXSIb+nU=" crossorigin="anonymous"></script>
<script src="{{ asset('js/cancel.js') }}"></script>
@endsection