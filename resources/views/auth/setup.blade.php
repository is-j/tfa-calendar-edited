@php
use App\Models\User;
use App\Models\Subject;

@endphp

@extends('layouts.auth')

@section('content')
<div class="card form-accounts mx-3 shadow">
    <div class="card-body">
        <h5 class="card-title">
            <div class="d-inline-block style-brand text-dark shadow-sm p-2">tfa-calendar</div>&nbsp;&nbsp;{{ __('Setup') }}
        </h5>
        <form class="needs-validation text-start" method="POST" action="{{ route('setup') }}" novalidate>
            @csrf
            @if(User::find(Auth::user()->id)->role() == 'student')
            <div class="my-3">
                <p>
                    By creating this account, I acknowledge that tutoring is a privilege, and I will try to show up at my session
                    as much as possible. I acknowledge that a no show up for 2 times without
                    cancellation 4 hours in advance will result in a 7 day withhold of my tutoring
                    privilege.
                </p>
            </div>
            <div class="mb-3">
                <div class="form-check">
                    <input class="form-check-input @error('terms') is-invalid @enderror" type="checkbox" name="terms" type="checkbox" id="terms" required>
                    <label class="form-check-label" for="terms">
                        I agree to the terms.
                    </label>
                </div>
                @error('terms')
                <span class="invalid-feedback">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>
            @elseif(User::find(Auth::user()->id)->role() == 'tutor')
            <div class="my-3">
                <input type="text" class="form-control @error('meeting_link') is-invalid @enderror" name="meeting_link" value="{{ old('meeting_link') }}" placeholder="Meeting link" required>
                <small class="form-text text-muted">
                    E.g. Personal Zoom or Google Meet Links.
                </small>
                @error('meeting_link')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>
            <div class="mb-3">
                <textarea class="form-control @error('bio') is-invalid @enderror" name="bio" placeholder="Introduce yourself to students" rows="3" maxlength="1000" required>{{ old('bio') }}</textarea>
                <small class="form-text text-muted">
                    Limit 1000 characters.
                </small>
                @error('bio')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>
            <div class="mb-3">
                <select class="form-select @error('subject') is-invalid @enderror" name="subject" required>
                    @foreach(Subject::get() as $item)
                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                    @endforeach
                </select>
                <small class="form-text text-muted">
                    You can select more subjects to tutor later.
                </small>
                @error('subject')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>
            @endif
            <div class="d-grid">
                <button class="btn btn-primary" type="submit">Submit</button>
            </div>
        </form>
    </div>
</div>
@endsection