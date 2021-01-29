@php
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\DB;
@endphp
@extends('layouts.app')

@section('content')
<div class="container-md">
    <div class="input-group mb-3 shadow-sm">
        <span class="input-group-text"><i data-feather="search"></i></span>
        <input type="text" class="form-control" name="search" placeholder="Search by #, name, email, or role">
    </div>
    <table class="table table-striped">
        <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col">Name</th>
                <th scope="col">Email</th>
                <th scope="col">Role</th>
                <th scope="col">Strikes</th>
            </tr>
        </thead>
        <tbody>
            @foreach (User::where('role_id', '!=', '1')->get() as $item)
            <tr>
                <th scope="row">{{$item->id}}</th>
                <td>{{ $item->name }}</td>
                <td>{{ $item->email }}</td>
                <td>{{ ucfirst(Role::find($item->role_id)->name) }}</td>
                @if (DB::table(Role::find($item->role_id)->name . 's')->where('user_id', $item->id)->exists())
                <td>{{ DB::table(Role::find($item->role_id)->name . 's')->where('user_id', $item->id)->first()->strikes }}</td>
                @else
                <td>NULL</td>
                @endif
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection

@section('scripts')
<script src="{{ asset('js/users.js') }}"></script>
@endsection