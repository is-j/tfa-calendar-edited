@php
use App\Models\Role;
use App\Models\User;
use App\Models\Probation;
@endphp
@extends('layouts.app')

@section('content')
<div class="container-md">
    <div class="input-group mb-3 shadow">
        <span class="input-group-text"><i data-feather="search"></i></span>
        <input type="text" class="form-control" name="search" placeholder="Search by #, name, role, or history">

    </div>
    <small><i data-feather="check"></i><span class="align-middle"> means currently under probation, while </span><i data-feather="x"></i><span class="algin-middle"> means previously was, but is not anymore. The history represents how many times the user has been under probation before.</span></small>
    <table class="table table-striped mt-3">
        <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col">Name</th>
                <th scope="col">Role</th>
                <th scope="col">Status</th>
                <th scope="col">History</th>
            </tr>
        </thead>
        <tbody>
            @foreach (Probation::get() as $item)
            @if (date('Y-m-d H:i:s', strtotime($item->end)) > date('Y-m-d H:i:s'))
            <tr>
                <th scope="row">{{ $item->user_id }}</th>
                <td>{{ User::find($item->user_id)->name }}</td>
                <td>{{ ucfirst(Role::find(User::find($item->user_id)->role_id)->name) }}</td>
                <td><i data-feather="check"></i></td>
                <td>{{ $item->history }}</td>
            </tr>
            @else
            <tr>
                <th scope="row">{{ $item->user_id }}</th>
                <td>{{ User::find($item->user_id)->name }}</td>
                <td>{{ ucfirst(Role::find(User::find($item->user_id)->role_id)->name) }}</td>
                <td><i data-feather="x"></i></td>
                <td>{{ $item->history }}</td>
            </tr>
            @endif
            @endforeach
        </tbody>
    </table>
</div>
@endsection

@section('scripts')
<script src="{{ asset('js/probations.js') }}"></script>
@endsection