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
            <ul class="nav nav-pills mb-3" role="tablist">
                <li class="nav-item" role="presentation">
                    <a class="nav-link active" id="account-tab" data-bs-toggle="tab" href="#account" role="tab" aria-controls="account" aria-selected="true">Account</a>
                </li>
                @if (User::find(Auth::user()->id)->role() == 'tutor')
                <li class="nav-item" role="presentation">
                    <a class="nav-link" id="subjects-tab" data-bs-toggle="tab" href="#subjects" role="tab" aria-controls="subjects" aria-selected="false">Subjects</a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link" id="information-tab" data-bs-toggle="tab" href="#information" role="tab" aria-controls="information" aria-selected="false">Information</a>
                </li>
                @elseif (User::find(Auth::user()->id)->role() == 'student')
                <li class="nav-item" role="presentation">
                    <a class="nav-link" id="agreement-tab" data-bs-toggle="tab" href="#agreement" role="tab" aria-controls="agreement" aria-selected="false">Agreement</a>
                </li>
                @endif
            </ul>
            <div class="tab-content">
                <div class="tab-pane active" id="account" role="tabpanel" aria-labelledby="account-tab">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item">Name: {{ Auth::user()->name }}</li>
                        <li class="list-group-item">Email: {{ Auth::user()->email }}</li>
                        <li class="list-group-item">Role: {{ ucfirst(User::find(Auth::user()->id)->role()) }}</li>
                        <li class="list-group-item"><a class="btn btn-danger" href="{{ route('reset') }}">Reset password</a></li>
                    </ul>
                </div>
                @if (User::find(Auth::user()->id)->role() == 'tutor')
                <div class="tab-pane" id="subjects" role="tabpanel" aria-labelledby="subjects-tab">
                    <div class="row">
                        <div class="col-md">
                            <ul class="list-group mt-3" id="searchInput">
                                <li class="list-group-item">
                                    <b>My subjects</b>
                                </li>
                            </ul>
                            <ul class="list-group mt-3 subjectContent" id="mainContent">
                                @foreach(Tutor::find(Auth::user()->id)->subjects() as $item)
                                <li class="list-group-item">{{ Subject::find($item)->name }}<span class="float-end toggle-true" data-subject="{{ $item }}" data-toggle="true"><i data-feather="minus"></i></span></li>
                                @endforeach
                                <div class="text-danger mt-2" id="subjectError">
                                    You must have at least one subject to tutor.
                                </div>
                            </ul>
                        </div>
                        <div class="col-md">
                            <ul class="list-group mt-3" id="searchInput">
                                <div class="input-group">
                                    <span class="input-group-text"><i data-feather="search"></i></span>
                                    <input type="text" class="form-control list-group-item" placeholder="Add subjects...">
                                </div>
                            </ul>
                            <ul class="list-group mt-3 subjectContent" id="searchContent">
                                @foreach(Subject::get() as $item)
                                @if (!in_array($item->id,Tutor::find(Auth::user()->id)->subjects()))
                                <li class="list-group-item">{{ $item->name }}<span class="float-end toggle-false" data-subject="{{ $item->id }}" data-toggle="false"><i data-feather="plus"></i></span></li>
                                @endif
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="tab-pane" id="information" role="tabpanel" aria-labelledby="information-tab">
                    <form class="needs-validation" id="informationForm" novalidate>
                        <fieldset>
                            <div class="my-3">
                                <input type="text" class="form-control" name="meeting_link" value="{{ Tutor::select('meeting_link')->find(Auth::user()->id)->first()->meeting_link }}" placeholder="Meeting link" required>
                                <small class="form-text text-muted">
                                    E.g. Personal Zoom or Google Meet Links.
                                </small>
                            </div>
                            <div>
                                <textarea class="form-control" name="bio" placeholder="Introduce yourself to students" rows="3" maxlength="1000" required>{{ Tutor::select('bio')->find(Auth::user()->id)->first()->bio }}</textarea>
                                <small class="form-text text-muted">
                                    Limit 1000 characters.
                                </small>
                            </div>
                        </fieldset>
                        <div class="text-end">
                            <button type="submit" class="btn btn-primary align-middle">Update information</button>
                        </div>
                    </form>
                </div>
                @elseif (User::find(Auth::user()->id)->role() == 'student')
                <div class="tab-pane" id="agreement" role="tabpanel" aria-labelledby="agreement-tab">
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