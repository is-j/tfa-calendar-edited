@php
use App\Models\User;
use App\Models\Subject;
use Illuminate\Support\Facades\Auth;
@endphp
@extends('layouts.app')

@section('content')
<div class="w-full" x-data="{tab: 'profile'}">
    <div class="flex mb-0">
        <button class="w-full mr-3 p-3 text-lg" type="button" x-bind:class="{'btn-positive': tab == 'profile', 'btn-neutral': tab != 'profile'}" @click="tab='profile'">
            Profile
        </button>
        @if (Auth::user()->role->name == 'tutor')
        <button class="w-full mr-3 p-3 text-lg" type="button" x-bind:class="{'btn-positive': tab == 'subjects', 'btn-neutral': tab != 'subjects'}" @click="tab='subjects'">
            Subjects
        </button>
        <button class="w-full p-3 text-lg" type="button" x-bind:class="{'btn-positive': tab == 'information', 'btn-neutral': tab != 'information'}" @click="tab='information'">
            Information
        </button>
        @elseif (Auth::user()->role->name== 'student')
        <button class="w-full p-3 text-lg" type="button" x-bind:class="{'btn-positive': tab == 'terms', 'btn-neutral': tab != 'terms'}" @click="tab='terms'">
            Terms
        </button>
        @endif
    </div>
    <div class="mt-4 shadow-lg bg-gray-100 rounded-md p-4">
        <div class="w-full h-full" x-show="tab == 'profile'">
            <ul class="list-group list-group-flush">
                <li class="list-group-item">Name: {{ Auth::user()->name }}</li>
                <li class="list-group-item">Email: {{ Auth::user()->email }}</li>
                <li class="list-group-item">Role: {{ ucfirst(Auth::user()->role->name) }}</li>
                <!--<li class="list-group-item"><a class="btn btn-danger" href="#">Reset password</a></li>-->
            </ul>
        </div>
        @if (Auth::user()->role->name == 'tutor')
        <div class="w-full h-full" x-show="tab == 'subjects'">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <div class="form-element mb-3">
                        <h3 class="text-base"><b>My subjects</b></h3>
                    </div>
                    <ul class="list-group subject-content h-full md:h-96" id="mainContent">
                        <div class="text-red-700 text-base ml-3 mt-3 opacity-0 hidden transition-all duration-500 ease-in-out" id="subjectError">
                            <span>You must have at least one subject to tutor.</span>
                        </div>
                    </ul>
                </div>
                <div>
                    <div class="relative flex w-full flex-wrap items-stretch mb-3">
                        <span class="z-10 h-full leading-snug font-normal absolute text-center text-gray-400 absolute bg-transparent rounded text-base items-center justify-center w-8 pl-3" style="padding-top:14px;">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                            </svg>
                        </span>
                        <input type="text" class="form-element" id="searchInput" style="padding-left:2.7rem;" placeholder="Search subjects..." autocomplete="off">
                    </div>
                    <ul class="list-group subject-content h-full md:h-96" id="searchContent">
                    </ul>
                </div>
            </div>
        </div>
        <div class="w-full h-full" x-show="tab == 'information'">
            <form class="needs-validation" id="informationForm" novalidate>
                <fieldset>
                    <div class="form-floating my-3">
                        <input type="text" class="form-element" name="meeting_link" value="{{ Auth::user()->tutor->meeting_link }}" placeholder="Meeting link" required>
                        <label>Meeting link</label>
                        <small class="text-help">
                            E.g. Personal Zoom or Google Meet Links.
                        </small>
                    </div>
                    <div class="form-floating">
                        <textarea class="form-element" name="bio" placeholder="Introduce yourself to students" rows="3" maxlength="1000" required>{{ Auth::user()->tutor->bio }}</textarea>
                        <label>Bio</label>
                        <small class="text-help">
                            Limit 1000 characters.
                        </small>
                    </div>
                </fieldset>
                <div class="text-right">
                    <button type="submit" class="btn-positive p-3 inline-flex items-center">Update information</button>
                </div>
            </form>
        </div>
        @elseif (Auth::user()->role->name== 'student')
        <div class="w-full h-full" x-show="tab == 'terms'">
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
@endsection

@section('scripts')
<script src="{{ mix('js/settings.js') }}"></script>
@endsection