@php
use App\Models\Role;
use App\Models\Slot;
use App\Models\User;
use App\Models\Report;
@endphp
@extends('layouts.app')

@section('content')
<div class="container-md">
    <div class="input-group mb-3 shadow">
        <span class="input-group-text"><i data-feather="search"></i></span>
        <input type="text" class="form-control" name="search" placeholder="Search by reporter, reported user, or dates">

    </div>
    <small><span class="align-middle">Use </span><i data-feather="check"></i><span class="align-middle"> to confirm the report, adding a strike to the reported user; else, use </span><i data-feather="x"></i><span class="align-middle">. All dates and times are in UTC.</span></small>
    <table class="table table-striped mt-3">
        <thead>
            <tr>
                <th scope="col">Reporter</th>
                <th scope="col">Reported user</th>
                <th scope="col">Slot date</th>
                <th scope="col">Report date</th>
                <th scope="col" class="text-center">Confirm/Deny</th>
            </tr>
        </thead>
        <tbody>
            @foreach (Report::get() as $item)
            <tr class="align-middle">
                <td scope="row">{{ User::find($item->reporter_id)->name }}; {{ ucfirst(Role::find(User::find($item->reporter_id)->role_id)->name) }}</td>
                <td>{{ User::find($item->reported_id)->name }}; {{ ucfirst(Role::find(User::find($item->reported_id)->role_id)->name) }}</td>
                <td>{{ Slot::find($item->event_id)->start }}</td>
                <td>{{ $item->created_at }}</td>
                <td class="text-center"><button type="button" class="btn btn-success me-2" data-action="confirm" data-eventid="{{ $item->event_id }}"><i data-feather="check"></i></button><button type="button" class="btn btn-danger" data-action="deny" data-eventid="{{ $item->event_id }}"><i data-feather="x"></i></button></td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection

@section('scripts')
<script src="{{ asset('js/reports.js') }}"></script>
@endsection