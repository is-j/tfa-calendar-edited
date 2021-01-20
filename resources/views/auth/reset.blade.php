@extends('layouts.auth')

@section('content')
<div class="card form-accounts mx-3 shadow">
    <div class="card-body">
        <h5 class="card-title">
            <div class="d-inline-block style-brand text-dark shadow-sm p-2">tfa-calendar</div>&nbsp;&nbsp;{{ __('Reset password') }}
        </h5>
        <form class="needs-validation text-start" method="POST" action="{{ route('reset') }}" novalidate>
            @csrf
            <div class="my-3">
                <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="Current password" required autocomplete="password">
                @error('password')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>
            <div class="mb-3">
                <input type="password" name="new_password" class="form-control @error('new_password') is-invalid @enderror" placeholder="New password" required autocomplete="new-password">
                @error('new_password')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>
            <div class="d-grid text-center">
                <button type="submit" class="btn btn-danger mb-3">
                    {{ __('Reset password') }}
                </button>
                <a class="btn btn-primary" href="{{ route('settings') }}">
                    {{ __('Back to settings') }}
                </a>
                <p class="mt-3 mb-0">Forgot password? Contact an adminstrator.</p>
            </div>
        </form>
    </div>
</div>
@endsection