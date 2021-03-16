@extends('layouts.auth')

@section('styles')
@if (session('status') == 'password-updated')
<script>
    const temp = document.createElement('form');
    const data = {
        _token: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        alert: 'Your password was successfully updated.'
    };
    document.head.appendChild(temp);
    temp.method = 'POST';
    temp.action = '/dashboard';
    for (let name in data) {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = name;
        input.value = data[name];
        temp.appendChild(input);
    }
    temp.submit();
</script>
@endif
@endsection

@section('content')
<h2 class="text-xl select-none">
    <span class="inline-block text-white font-black tracking-wide p-3 bg-gray-600 rounded-md">tfa-calendar</span>
    &nbsp;&nbsp;<span class="inline-block p-3 bg-gray-300 rounded-md">Update password</span>
</h2>
<form class="text-left" method="POST" action="{{ route('user-password.update') }}" novalidate>
    @csrf
    @method('PUT')
    <div class="my-5">
        <input class="form-element @error('current_password', 'updatePassword') is-invalid @enderror" name="current_password" type="password" placeholder="Current password" required autocomplete="current-password" />
        @error('current_password', 'updatePassword')
        <span class="invalid-feedback" role="alert">
            <strong>{{ $message }}</strong>
        </span>
        @enderror
    </div>
    <div class="mb-5">
        <input class="form-element @error('password', 'updatePassword') is-invalid @enderror" name="password" type="password" placeholder="New password" required autocomplete="new-password">
        @error('password', 'updatePassword')
        <span class="invalid-feedback" role="alert">
            <strong>{{ $message }}</strong>
        </span>
        @enderror
    </div>
    <div class="mb-5">
        <input class="form-element @error('password_confirmation', 'updatePassword') is-invalid @enderror" name="password_confirmation" type="password" placeholder="Confirm password" required autocomplete="new-password">
        @error('password_confirmation', 'updatePassword')
        <span class="invalid-feedback" role="alert">
            <strong>{{ $message }}</strong>
        </span>
        @enderror
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <button class="btn-neutral text-base tracking-wide w-full h-12" type="button" onclick="location.href=`{{ route('settings') }}`">
            Back to settings
        </button>
        <button class="btn-positive text-base tracking-wide w-full h-12" type="submit">Update password</button>
    </div>
</form>
@endsection