@extends('layouts.auth')

@section('content')
<div id="alertContainer" x-data="{open: @if (session('status')) true @else false @endif}" x-cloak>
    <div id="alertBox" class="absolute top-0 left-1/2 z-10 max-w-md w-full mt-12 text-white px-6 py-4 border-0 rounded-lg shadow-lg mb-4 bg-gray-700 select-none" style="transform:translateX(-50%);" x-show="open" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-300" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
        <span class="inline-block align-middle mr-8 pointer-events-none" id="alertText">
            @if (session('status'))
                {{ session('status') }}
            @endif
        </span>
        <button class="absolute bg-transparent text-2xl font-semibold leading-none right-0 top-0 mt-4 mr-6 outline-none focus:outline-none cursor-pointer" @click="open=!open">
            <span>×</span>
        </button>
    </div>
</div>
<h2 class="text-xl select-none">
    <span class="inline-block text-white font-black tracking-wide p-3 bg-gray-600 rounded-md">tfa-calendar</span>
    &nbsp;&nbsp;<span class="inline-block p-3 bg-gray-300 rounded-md">Forgot password</span>
</h2>
<form class="text-left" method="POST" action="{{ route('password.request') }}" novalidate>
    @csrf
    <div class="my-5">
        <small class="text-help">Please type in your account email and you will receive an email with instructions to reset your password.</small>
        <input class="form-element mt-5 @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" type="email" placeholder="Email" required autocomplete="email" autofocus />
        @error('email')
        <span class="invalid-feedback" role="alert">
            <strong>{{ $message }}</strong>
        </span>
        @enderror
    </div>
    <button class="btn-positive text-base tracking-wide w-full h-12" type="submit">Send email</button>
</form>
<p class="mt-3 mb-0">Don't have an account? <a class="link-custom" href="{{ route('register') }}">Register here.</a></p>
@endsection