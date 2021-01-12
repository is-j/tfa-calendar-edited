@php
use App\Models\User;
use App\Models\Tutor;
use App\Models\Subject;
use Illuminate\Support\Facades\Auth;
@endphp
@extends('layouts.app')

@section('content')
<div class="container-md">
    <div class="card shadow">
        <div class="card-header" style="font-size:18px;">{{ __('Settings') }}</div>
        <div class="card-body">
            <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                <li class="nav-item" role="presentation">
                    <a class="nav-link active" id="pills-account-tab" data-toggle="pill" href="#pills-account" role="tab" aria-controls="pills-account" aria-selected="true">Account</a>
                </li>
                @if (User::find(Auth::user()->id)->role() == 'tutor')
                <li class="nav-item" role="presentation">
                    <a class="nav-link" id="pills-subjects-tab" data-toggle="pill" href="#pills-subjects" role="tab" aria-controls="pills-subjects" aria-selected="false">Subjects</a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link" id="pills-information-tab" data-toggle="pill" href="#pills-information" role="tab" aria-controls="pills-information" aria-selected="false">Information</a>
                </li>
                @elseif (User::find(Auth::user()->id)->role() == 'student')
                <li class="nav-item" role="presentation">
                    <a class="nav-link" id="pills-agreement-tab" data-toggle="pill" href="#pills-agreement" role="tab" aria-controls="pills-agreement" aria-selected="false">Agreement</a>
                </li>
                @endif
            </ul>
            <div class="tab-content" id="pills-tabContent">
                <div class="tab-pane fade show active" id="pills-account" role="tabpanel" aria-labelledby="pills-account-tab">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item">Name: {{ Auth::user()->name }}</li>
                        <li class="list-group-item">Email: {{ Auth::user()->email }}</li>
                        <li class="list-group-item">Role: {{ ucfirst(User::find(Auth::user()->id)->role()) }}</li>
                    </ul>
                    <div class="text-right">
                        <a class="btn btn-danger" href="{{ route('reset') }}">Reset password</a>
                    </div>
                </div>
                @if (User::find(Auth::user()->id)->role() == 'tutor')
                <div class="tab-pane fade" id="pills-subjects" role="tabpanel" aria-labelledby="pills-subjects-tab">
                    <div class="row">
                        <div class="col-md">
                            <ul class="list-group mt-3" id="searchInput">
                                <li class="list-group-item">
                                    <b>My subjects</b>
                                </li>
                            </ul>
                            <ul class="list-group mt-3 subjectContent" id="mainContent">
                                @foreach(Tutor::find(Auth::user()->id)->subjects() as $item)
                                <li class="list-group-item">{{ Subject::find($item)->name }}<span class="float-right toggle-true" data-subject="{{ $item }}" data-toggle="true"><i data-feather="minus"></i></span></li>
                                @endforeach
                                <div class="text-danger mt-2" id="subjectError" style="font-size:16px;">
                                    You must have at least one subject to tutor.
                                </div>
                            </ul>
                        </div>
                        <div class="col-md">
                            <ul class="list-group mt-3" id="searchInput">
                                <div class="input-group" style="width:100%;">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i data-feather="search"></i></span>
                                    </div>
                                    <input type="text" class="form-control list-group-item" style="height:49px;" placeholder="Add subjects...">
                                </div>
                            </ul>
                            <ul class="list-group mt-3 subjectContent" id="searchContent">
                                @foreach(Subject::get() as $item)
                                @if (!in_array($item->id,Tutor::find(Auth::user()->id)->subjects()))
                                <li class="list-group-item">{{ $item->name }}<span class="float-right toggle-false" data-subject="{{ $item->id }}" data-toggle="false"><i data-feather="plus"></i></span></li>
                                @endif
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade" id="pills-information" role="tabpanel" aria-labelledby="pills-information-tab">
                    <form class="needs-validation" id="informationForm" novalidate>
                        <fieldset>
                            <div class="form-group">
                                <input type="url" class="form-control" name="meeting_link" value="{{ Tutor::select('meeting_link')->find(Auth::user()->id)->first()->meeting_link }}" placeholder="Meeting link" required>
                                <small class="form-text text-muted">
                                    E.g. Personal Zoom or Google Meet Links. Must begin with "https://".
                                </small>
                            </div>
                            <div class="form-group">
                                <textarea class="form-control" name="bio" placeholder="Introduce yourself to students" rows="3" maxlength="1000" required>{{ Tutor::select('bio')->find(Auth::user()->id)->first()->bio }}</textarea>
                                <small class="form-text text-muted">
                                    Limit 1000 characters.
                                </small>
                            </div>
                        </fieldset>
                        <div class="text-right">
                            <button type="submit" class="btn btn-primary align-middle">Update information</button>
                        </div>
                    </form>
                </div>
                @elseif (User::find(Auth::user()->id)->role() == 'student')
                <div class="tab-pane fade" id="pills-agreement" role="tabpanel" aria-labelledby="pills-agreement-tab">
                    <b>When you created this account, you automatically agreed to the following terms*:</b>
                    <br>
                    <br>
                    I acknowledge that tutoring is a privilege, and I will try to show up at my session
                    as much as possible. I acknowledge that a no show up for 2 times without
                    cancellation 4 hours in advance will result in a 7 day withhold of my tutoring
                    privilege.
                    <br>
                    <br>
                    <i>*Note that these terms were presented to you on account setup.</i>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('js/settings.js') }}"></script>
@endsection