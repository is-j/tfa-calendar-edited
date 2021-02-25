@php
use App\Models\Subject;
@endphp

@extends('layouts.auth')

@section('content')
<h2 class="text-xl select-none">
    <span class="inline-block text-white font-black tracking-wide p-3 bg-gray-600 rounded-md">tfa-calendar</span>
    &nbsp;&nbsp;<span class="inline-block p-3 bg-gray-300 rounded-md">Setup</span>
</h2>
<form class="text-left" method="POST" action="{{ route('setup') }}" novalidate>
    @csrf
    @if(Auth::user()->role->name == 'student')
    <div class="my-5">
        <p>
            By creating this account, I acknowledge that tutoring is a privilege, and I will try to show up at my session
            as much as possible. I acknowledge that a no show up for 2 times without
            cancellation 4 hours in advance will result in a 7 day withhold of my tutoring
            privilege.
        </p>
    </div>
    <div class="flex pl-5 mb-5">
        <input class="form-check mt-1 @error('terms') is-invalid @enderror" type="checkbox" name="terms" id="terms">
        <label class="text-gray-700 ml-2" for="terms">I agree to these terms.</label>
    </div>
    @elseif(Auth::user()->role->name == 'tutor')
    <div class="my-5">
        <input type="text" class="form-element @error('meeting_link') is-invalid @enderror" name="meeting_link" value="{{ old('meeting_link') }}" placeholder="Meeting link" required>
        <small class="text-help">
            E.g. Personal Zoom or Google Meet Links.
        </small>
        @error('meeting_link')
        <span class="invalid-feedback" role="alert">
            <strong>{{ $message }}</strong>
        </span>
        @enderror
    </div>
    <div class="mb-5">
        <textarea class="form-element @error('bio') is-invalid @enderror" name="bio" placeholder="Introduce yourself to students" rows="3" maxlength="1000" required>{{ old('bio') }}</textarea>
        <small class="text-help">
            Limit 1000 characters.
        </small>
        @error('bio')
        <span class="invalid-feedback" role="alert">
            <strong>{{ $message }}</strong>
        </span>
        @enderror
    </div>
    <div class="mb-5">
        <select class="form-select @error('subject') is-invalid @enderror" name="subject" required>
            @foreach(Subject::get() as $item)
            <option value="{{ $item->id }}">{{ $item->name }}</option>
            @endforeach
        </select>
        <small class="text-help">
            You can select more subjects to tutor later.
        </small>
        @error('subject')
        <span class="invalid-feedback" role="alert">
            <strong>{{ $message }}</strong>
        </span>
        @enderror
    </div>
    @endif
    <button class="btn-positive text-base tracking-wide w-full h-12" type="submit">Submit</button>
</form>
@endsection