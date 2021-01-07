@extends('layouts.auth')

@section('content')
<div class="card form-accounts mx-3 shadow">
    <div class="card-body">
        <h5 class="card-title">
            <div class="d-inline-block bg-secondary text-light p-2 border-clean">tfa-calendar</div>&nbsp;&nbsp;{{ __('Register') }}
        </h5>
        <form class="needs-validation text-left" method="POST" action="{{ route('register') }}" novalidate>
            @csrf
            <div class="form-group">
                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="Full Name" required autocomplete="name" autofocus>
                @error('name')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>
            <div class="form-group">
                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" placeholder="Email" required autocomplete="email" autofocus>
                @error('email')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>
            <div class="form-group">
                <input type="text" name="code" class="form-control @error('code') is-invalid @enderror" value="{{ old('code') }}" placeholder="Account Code" required>
                @error('code')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>
            <div class="form-group">
                <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="Password" required autocomplete="new-password">
                @error('password')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>
            <div class="form-group">
                <input type="password" name="password_confirmation" class="form-control @error('password') is-invalid @enderror" placeholder="Confirm Password" required autocomplete="new-password">
                @error('password')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>
            <button type="submit" class="btn btn-primary btn-block">
                {{ __('Register') }}
            </button>
            <div class="text-center">
                <p class="mt-3 mb-0">Already have an account? <a href="{{ route('login') }}">Login here.</a></p>
            </div>
        </form>
    </div>
</div>
@endsection