@php
use App\Models\User;
@endphp

@extends('layouts.app')

@section('content')


<!-- calendar container -->
<div class="container-md">
    <div id="calendar"></div>
</div>

<!-- tutor: create slot modal -->
<div class="modal fade" id="createSlotModal" tabindex="-1" aria-labelledby="createSlotModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createSlotModalLabel">Create slot</h5>
            </div>
            <div class="modal-body">
                <form class="needs-validation ajax" id="createSlotForm" novalidate>
                    <div class="form-group">
                        <input type="datetime-local" class="form-control" name="start" required>
                        <div class="invalid-feedback">
                            Slot must be created at least 2 hours in advance.
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="repeat" id="repeatCreate">
                            <label class="form-check-label" for="repeatCreate">
                                Repeat this slot on this day of the week at this time for the next 20 weeks.
                            </label>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
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
                    <div class="input-group mb-3" id="startDelete">
                        <div class="input-group-prepend">
                            <span class="input-group-text">Date/Time</span>
                        </div>
                        <input type="datetime-local" class="form-control" name="start" disabled>
                    </div>
                    <div class="input-group mb-3" id="studentnameDelete">
                        <div class="input-group-prepend">
                            <span class="input-group-text">Student name</span>
                        </div>
                        <input type="text" class="form-control" name="studentname" disabled>
                    </div>
                    <div class="input-group mb-3" id="studentemailDelete">
                        <div class="input-group-prepend">
                            <span class="input-group-text">Student email</span>
                        </div>
                        <input type="text" class="form-control" name="studentemail" disabled>
                    </div>
                    <div class="input-group mb-3" id="subjectDelete">
                        <div class="input-group-prepend"> <span class="input-group-text">Subject</span> </div><input type="text" class="form-control" name="subject" disabled>
                    </div>
                    <div class="input-group" id="infoDelete">
                        <div class="input-group-prepend">
                            <span class="input-group-text">Topic needs</span>
                        </div>
                        <textarea class="form-control" maxlength="1000" rows="3" name="info" disabled required></textarea>
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
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-danger" id="deleteSlotBtn">Delete</button>
            </div>
        </div>
    </div>
</div>

<!-- student: unclaim slot modal -->
<div class="modal fade" id="unclaimSlotModal" tabindex="-1" aria-labelledby="unclaimSlotModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="unclaimSlotModalLabel"></h5>
            </div>
            <div class="modal-body">
                <form class="needs-validation ajax" id="unclaimSlotForm" novalidate>
                    <div class="input-group mb-3" id="startUnclaim">
                        <div class="input-group-prepend">
                            <span class="input-group-text">Date/Time</span>
                        </div>
                        <input type="datetime-local" class="form-control" name="start" disabled>
                    </div>
                    <div class="input-group mb-3" id="tutornameUnclaim">
                        <div class="input-group-prepend">
                            <span class="input-group-text">Tutor name</span>
                        </div>
                        <input type="text" class="form-control" name="tutorname" disabled>
                        <div class="input-group-append">
                            <span class="input-group-text"></span>
                        </div>
                    </div>
                    <div class="input-group mb-3" id="tutorbioUnclaim">
                        <div class="input-group-prepend">
                            <span class="input-group-text">Tutor bio</span>
                        </div>
                        <textarea class="form-control" maxlength="1000" rows="3" disabled></textarea>
                    </div>
                    <div class="input-group mb-3" id="tutoremailUnclaim">
                        <div class="input-group-prepend">
                            <span class="input-group-text">Tutor email</span>
                        </div>
                        <input type="text" class="form-control" name="tutoremail" disabled>
                    </div>
                    <div class=" input-group mb-3" id="subjectUnclaim">
                        <div class="input-group-prepend">
                            <span class="input-group-text">Subject</span>
                        </div>
                        <input type="text" class="form-control" name="subject" name="subject" disabled>
                    </div>
                    <div class="input-group" id="infoUnclaim">
                        <div class="input-group-prepend">
                            <span class="input-group-text">Topic needs</span>
                        </div>
                        <textarea class="form-control" maxlength="1000" rows="3" name="info" disabled required></textarea>
                    </div>
                    <div class="input-group mt-3" id="meetinglinkUnclaim">
                        <a class="btn btn-info btn-block" href="#" target="_blank"><i data-feather="video"></i>&nbsp;&nbsp;<span class="align-middle">Meeting link</span></a>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
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
                    <div class="input-group mb-3" id="startClaim">
                        <div class="input-group-prepend">
                            <span class="input-group-text">Date/Time</span>
                        </div>
                        <input type="datetime-local" class="form-control" name="start" disabled>
                    </div>
                    <div class="input-group mb-3" id="tutornameClaim">
                        <div class="input-group-prepend">
                            <span class="input-group-text">Tutor name</span>
                        </div>
                        <input type="text" class="form-control" name="tutorname" disabled>
                    </div>
                    <div class="input-group mb-3" id="tutorbioClaim">
                        <div class="input-group-prepend">
                            <span class="input-group-text">Tutor bio</span>
                        </div>
                        <textarea class="form-control" maxlength="1000" rows="3" name="tutorbio" disabled></textarea>
                    </div>
                    <div class="input-group mb-3" id="subjectClaim">
                    </div>
                    <div class="input-group" id="infoClaim">
                        <div class="input-group-prepend">
                            <span class="input-group-text">Topic needs</span>
                        </div>
                        <textarea class="form-control" maxlength="1000" rows="3" name="info" required></textarea>
                    </div>
                    <small class="form-text text-muted">
                        Limit 1000 characters.
                    </small>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="claimSlotBtn">Claim</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.4.0/main.min.js" integrity="sha256-oenhI3DRqaPoTMAVBBzQUjOKPEdbdFFtTCNIosGwro0=" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/luxon@1.25.0/build/global/luxon.min.js" integrity="sha256-OVk2fwTRcXYlVFxr/ECXsakqelJbOg5WCj1dXSIb+nU=" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/jquery.redirect@1.1.4/jquery.redirect.min.js"></script>
@if (User::find(Auth::user()->id)->role() == 'tutor')
<script>
    var accounttype = 'tutor';
</script>
@elseif (User::find(Auth::user()->id)->role() == 'student')
<script>
    var accounttype = 'student';
</script>
@endif
<script src="{{ asset('js/dashboard.js') }}"></script>
@endsection