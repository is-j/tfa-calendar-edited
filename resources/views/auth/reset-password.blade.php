@extends('layouts.auth')

@section('content')
<h2 class="text-xl select-none">
    <span class="inline-block text-white font-black tracking-wide p-3 bg-gray-600 rounded-md">tfa-calendar</span>
    &nbsp;&nbsp;<span class="inline-block p-3 bg-gray-300 rounded-md">Reset password</span>
</h2>
<form class="text-left" method="POST" action="{{ route('password.update') }}" novalidate>
    @csrf
    <input type="hidden" name="token" value="{{ $request->route('token') }}">
    <input type="hidden" name="email" value="{{ $request->email }}">
    <div class="my-5">
        <input class="form-element @error('password') is-invalid @enderror" name="password" type="password" placeholder="New password" required autocomplete="new-password">
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
    <button class="btn-positive text-base tracking-wide w-full h-12" type="submit">Reset password</button>
</form>
@endsection