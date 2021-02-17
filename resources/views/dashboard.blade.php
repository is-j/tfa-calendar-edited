@php
use App\Models\User;
use App\Models\Tutor;
use App\Models\Subject;
@endphp

@extends('layouts.app')

@section('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@5.5.1/main.min.css" integrity="sha256-uq9PNlMzB+1h01Ij9cx7zeE2OR2pLAfRw3uUUOOPKdA=" crossorigin="anonymous">
@endsection

@section('content')
<div aria-live="polite" aria-atomic="true" class="bg-dark position-relative bd-example-toasts">
    <div class="toast-container position-absolute pe-3 start-50 translate-middle-x" style="top:-10px;">
        <div class="toast d-flex align-items-center text-white bg-primary border-0" id="toast" role="alert" aria-live="assertive" aria-atomic="true" style="z-index:1000;">
            <div class="toast-body">
            </div>
            <button type="button" class="btn-close btn-close-white ms-auto me-2" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    </div>
</div>

<!-- calendar container -->
<div class="container-md">
    <div id="calendar"></div>
</div>

@if (User::find(Auth::user()->id)->role() == 'tutor')
<!-- tutor: create slot modal -->
<div class="modal fade" id="createSlotModal" tabindex="-1" aria-labelledby="createSlotModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createSlotModalLabel">Create slot</h5>
            </div>
            <div class="modal-body">
                <form class="needs-validation ajax" id="createSlotForm" novalidate>
                    <div class="mb-3">
                        <input type="datetime-local" class="form-control" name="start" required>
                        <div class="invalid-feedback">
                            Slot must be created at least 6 hours in advance.
                        </div>
                    </div>
                    <div class="input-group mb-3" id="subjectClaim">
                        <span class="input-group-text">Subject</span>
                        <select class="form-select" name="subject">
                            @foreach(Tutor::find(Auth::user()->id)->subjects() as $item)
                            <option value="{{ $item }}">{{ Subject::find($item)->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="repeat" id="repeatCreate">
                        <label class="form-check-label" for="repeatCreate">
                            Repeat this slot on this day of the week at this time for the next 20 weeks.
                        </label>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="createSlotBtn">Create</button>
            </div>
        </div>
    </div>
</div>
<!-- tutor: delete slot modal -->
<div class="modal fade" id="deleteSlotModal" tabindex="-1" aria-labelledby="deleteSlotModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteSlotModalLabel"></h5>
            </div>
            <div class="modal-body">
                <form class="needs-validation ajax" id="deleteSlotForm" novalidate>
                    <div class="form-floating mb-3" id="startDelete">
                        <input type="datetime-local" class="form-control" name="start" disabled>
                        <label>Date/Time</label>
                    </div>
                    <div class="form-floating mb-3" id="studentnameDelete">
                        <input type="text" class="form-control" name="studentname" disabled>
                        <label>Student name</label>
                    </div>
                    <div class="form-floating mb-3" id="studentemailDelete">
                        <input type="text" class="form-control" name="studentemail" disabled>
                        <label>Student email</label>
                    </div>
                    <div class="form-floating mb-3" id="subjectDelete">
                        <input type="text" class="form-control" name="subject" disabled>
                        <label>Subject</label>
                    </div>
                    <div class="form-floating mb-3" id="infoDelete">
                        <textarea class="form-control" maxlength="1000" name="info" disabled></textarea>
                        <label>Topic needs</label>
                    </div>
                    <div class="d-grid" id="meetinglinkDelete">
                        <a class="btn btn-info" href="#" target="_blank"><i data-feather="video"></i>&nbsp;&nbsp;<span class="align-middle">Meeting link</span></a>
                    </div>
                    <div class="form-check" id="repeatDelete">
                        <input class="form-check-input" type="checkbox" name="repeat" id="repeatLabel">
                        <label class="form-check-label" for="repeatLabel">
                            Delete all repeating slots on this day of the week at this time after this slot.
                        </label>
                    </div>
                    <div class="d-none" id="claimedDelete" data-claimed=""></div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-danger" id="deleteSlotBtn">Delete</button>
            </div>
        </div>
    </div>
</div>
@elseif (User::find(Auth::user()->id)->role() == 'student')
<!-- student: unclaim slot modal -->
<div class="modal fade" id="unclaimSlotModal" tabindex="-1" aria-labelledby="unclaimSlotModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="unclaimSlotModalLabel"></h5>
            </div>
            <div class="modal-body">
                <form class="needs-validation ajax" id="unclaimSlotForm" novalidate>
                    <div class="form-floating mb-3" id="startUnclaim">
                        <input type="datetime-local" class="form-control" name="start" disabled>
                        <label>Date/Time</label>
                    </div>
                    <div class="form-floating mb-3" id="tutornameUnclaim">
                        <input type="text" class="form-control" name="tutorname" disabled>
                        <label>Tutor name</label>
                    </div>
                    <div class="form-floating mb-3" id="tutoremailUnclaim">
                        <input type="text" class="form-control" name="tutoremail" disabled>
                        <label>Tutor email</label>
                    </div>
                    <div class="form-floating mb-3" id="tutorbioUnclaim">
                        <textarea class="form-control" maxlength="1000" name="tutorbio" disabled></textarea>
                        <label>Tutor bio</label>
                    </div>
                    <div class="form-floating mb-3" id="subjectUnclaim">
                        <input type="text" class="form-control" name="subject" disabled>
                        <label>Subject</label>
                    </div>
                    <div class="form-floating mb-3" id="infoUnclaim">
                        <textarea class="form-control" maxlength="1000" name="info" disabled required></textarea>
                        <label>Topic needs</label>
                    </div>
                    <div class="d-grid" id="meetinglinkUnclaim">
                        <a class="btn btn-info" href="#" target="_blank"><i data-feather="video"></i>&nbsp;&nbsp;<span class="align-middle">Meeting link</span></a>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-danger" id="unclaimSlotBtn">Unclaim</button>
            </div>
        </div>
    </div>
</div>
<!-- student: claim slot modal -->
<div class="modal fade" id="claimSlotModal" tabindex="-1" aria-labelledby="claimSlotModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="claimSlotModalLabel"></h5>
            </div>
            <div class="modal-body">
                <form class="needs-validation ajax" id="claimSlotForm" novalidate>
                    <div class="form-floating mb-3" id="startClaim">
                        <input type="datetime-local" class="form-control" name="start" disabled>
                        <label>Date/Time</label>
                    </div>
                    <div class="form-floating mb-3" id="tutornameClaim">
                        <input type="text" class="form-control" name="tutorname" disabled>
                        <label>Tutor name</label>
                    </div>
                    <div class="form-floating mb-3" id="tutorbioClaim">
                        <textarea class="form-control" maxlength="1000" name="tutorbio" disabled></textarea>
                        <label>Tutor bio</label>
                    </div>
                    <div class="form-floating mb-3" id="subjectClaim">
                        <input type="text" class="form-control" name="subject" disabled>
                        <label>Subject</label>
                    </div>
                    <div class="form-floating" id="infoClaim">
                        <textarea class="form-control" maxlength="1000" name="info" required></textarea>
                        <label>Topic needs</label>
                        <small class="form-text text-muted">
                            Limit 1000 characters.
                        </small>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="claimSlotBtn">Claim</button>
            </div>
        </div>
    </div>
</div>
@endif
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.5.1/main.min.js" integrity="sha256-rPPF6R+AH/Gilj2aC00ZAuB2EKmnEjXlEWx5MkAp7bw=" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/jquery.redirect@1.1.4/jquery.redirect.min.js"></script>
@if (User::find(Auth::user()->id)->role() == 'tutor')
<script>
    var accounttype = 'tutor';
    let createSlotModal = new bootstrap.Modal(document.getElementById('createSlotModal'));
    let deleteSlotModal = new bootstrap.Modal(document.getElementById('deleteSlotModal'));
</script>
@elseif (User::find(Auth::user()->id)->role() == 'student')
<script>
    var accounttype = 'student';
    let claimSlotModal = new bootstrap.Modal(document.getElementById('claimSlotModal'));
    let unclaimSlotModal = new bootstrap.Modal(document.getElementById('unclaimSlotModal'));
</script>
@elseif (User::find(Auth::user()->id)->role() == 'admin')
<script>
    var accounttype = 'admin';
</script>
@endif
<script src="{{ asset('js/dashboard.js') }}"></script>
@endsection