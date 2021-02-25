@extends('layouts.auth')

@section('content')
<h2 class="text-xl select-none">
    <span class="inline-block text-white font-black tracking-wide p-3 bg-gray-600 rounded-md">tfa-calendar</span>
    &nbsp;&nbsp;<span class="inline-block p-3 bg-gray-300 rounded-md">Register</span>
</h2>
<form class="text-left" method="POST" action="{{ route('register') }}" novalidate>
    @csrf
    <div class="my-5">
        <input type="text" name="name" class="form-element @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="Full name" required autocomplete="name" autofocus>
        @error('name')
        <span class="invalid-feedback" role="alert">
            <strong>{{ $message }}</strong>
        </span>
        @enderror
    </div>
    <div class="mb-5">
        <input class="form-element @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" type="email" placeholder="Email" required autocomplete="email" />
        @error('email')
        <span class="invalid-feedback" role="alert">
            <strong>{{ $message }}</strong>
        </span>
        @enderror
    </div>
    <div class="mb-5">
        <input class="form-element @error('password') is-invalid @enderror" name="password" type="password" placeholder="Password" required autocomplete="new-password" />
        @error('password')
        <span class="invalid-feedback" role="alert">
            <strong>{{ $message }}</strong>
        </span>
        @enderror
    </div>
    <div class="mb-5">
        <input class="form-element @error('password_confirmation') is-invalid @enderror" name="password_confirmation" type="password" placeholder="Confirm password" required autocomplete="new-password">
        @error('password_confirmation')
        <span class="invalid-feedback" role="alert">
            <strong>{{ $message }}</strong>
        </span>
        @enderror
    </div>
    <div class="mb-5">
        <input class="form-element @error('code') is-invalid @enderror" name="code" value="{{ old('code') }}" type="text" placeholder="Account code" required autocomplete="off">
        @error('code')
        <span class="invalid-feedback" role="alert">
            <strong>{{ $message }}</strong>
        </span>
        @enderror
    </div>
    <input class="hidden" name="offset" id="offset" type="number">
    <button class="btn-positive text-base tracking-wide w-full h-12" type="submit">Register</button>
</form>
<p class="mt-3 mb-0">Already have an account? <a class="link-custom" href="{{ route('login') }}">Login here.</a></p>
@endsection

@section('scripts')
<script src="{{ asset('js/auth/register.js') }}"></script>
@endsection