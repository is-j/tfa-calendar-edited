@extends('layouts.auth')

@section('content')
<h2 class="text-xl select-none">
    <span class="inline-block text-white font-black tracking-wide p-3 bg-gray-600 rounded-md">tfa-calendar</span>
    &nbsp;&nbsp;<span class="inline-block p-3 bg-gray-300 rounded-md">Login</span>
</h2>
<form class="text-left" method="POST" action="{{ route('login') }}" novalidate>
    @csrf
    <div class="my-5">
        <input class="form-element @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" type="email" placeholder="Email" required autocomplete="email" autofocus />
        @error('email')
        <span class="invalid-feedback" role="alert">
            <strong>{{ $message }}</strong>
        </span>
        @enderror
    </div>
    <div class="mb-5">
        <input class="form-element @error('password') is-invalid @enderror" name="password" type="password" placeholder="Password" required autocomplete="current-password" />
        @error('password')
        <span class="invalid-feedback" role="alert">
            <strong>{{ $message }}</strong>
        </span>
        @enderror
    </div>
    <button class="btn-positive text-base tracking-wide w-full h-12" type="submit">Login</button>
</form>
<p class="mt-3 mb-0">Don't have an account? <a class="link-custom" href="{{ route('register') }}">Register here.</a></p>
@endsection