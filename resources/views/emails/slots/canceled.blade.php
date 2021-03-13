@php
use App\Models\User;
@endphp
@component('mail::message')
# Slot Canceled

@component('mail::panel')
## General
<i> *note that all times are in your local timezone.</i>
<br>
<br>
@if ($type == 'delete')
@if ($role == 'tutor')
You canceled the session at {{ (new DateTime($slot['start']))->setTimezone(new DateTimeZone(User::find($slot['tutor_id'])->timezone))->format('D M j, Y g:i A') }} with student {{ User::find($slot['student_id'])->name }}. Please remember to avoid doing so after a student claims the slot.
@elseif ($role == 'student')
Your tutor {{ User::find($slot['tutor_id'])->name }} canceled the session at {{ (new DateTime($slot['start']))->setTimezone(new DateTimeZone(User::find($slot['student_id'])->timezone))->format('D M j, Y g:i A') }} with you. We apologize for the inconvenience.
@endif
@elseif ($type == 'unclaim')
@if ($role == 'tutor')
Your student {{ User::find($slot['student_id'])->name }} canceled the session at {{ (new DateTime($slot['start']))->setTimezone(new DateTimeZone(User::find($slot['tutor_id'])->timezone))->format('D M j, Y g:i A') }} with you.
@elseif ($role == 'student')
You canceled the session at {{ (new DateTime($slot['start']))->setTimezone(new DateTimeZone(User::find($slot['student_id'])->timezone))->format('D M j, Y g:i A') }} with tutor {{ User::find($slot['tutor_id'])->name }}. Please remember to avoid doing so after you claim the slot.
@endif
@endif
<br>
<br>
All of these changes are visible on the <a href="https://cal.tutoringforall.org" target="_blank">tfa-calendar website</a>.
@endcomponent

@component('mail::panel')
## Cancellation reason
{{ $reason }}
@endcomponent
@component('mail::panel')
## This is a Do Not Reply email
@if ($role == 'tutor') 
No emails sent to scheduler@tutoringforall.org will be read. If you have any questions or information, please send it to info@tutoringforall.org.
@elseif ($role == 'student')
No emails sent to scheduler@tutoringforall.org will be read. If you have any questions or information, please send it to info@tutoringforall.org.
@endif
{{ $slot['info'] }}
@endcomponent
@endcomponent