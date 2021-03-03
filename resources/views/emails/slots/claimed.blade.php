@php
use App\Models\User;
@endphp
@component('mail::message')
# Slot Claimed

@component('mail::panel')
## General
<i> *note that all times are in your local timezone.</i>
<br>
<br>
@if ($role == 'tutor')
The student {{ User::find($slot['student_id'])->name }} claimed the session at {{ date("D M j, Y g:i A", strtotime($slot['start'] . sprintf("%+d",User::find($slot['tutor_id'])->offset * 60) . " minutes")) }} with you. Please remember to avoid canceling.
<br>
<br>
Please go to the following meeting link at least 5 minutes before {{ date("D M j, Y g:i A", strtotime($slot['start'] . sprintf("%+d",User::find($slot['tutor_id'])->offset * 60) . " minutes")) }}. If this is not your preferred meeting link, please change it on your profile on the <a href="https://cal.tutoringforall.org" target="_blank">tfa-calendar website</a> before the session.
@elseif ($role == 'student')
You claimed the session at {{ date("D M j, Y g:i A", strtotime($slot['start'] . sprintf("%+d",User::find($slot['student_id'])->offset * 60) . " minutes")) }} with the tutor {{ User::find($slot['tutor_id'])->name }}. Please remember to avoid canceling.
<br>
<br>
Please go to the following meeting link at least 5 minutes before {{ date("D M j, Y g:i A", strtotime($slot['start'] . sprintf("%+d",User::find($slot['student_id'])->offset * 60) . " minutes")) }}. You can also access all this information on the <a href="https://cal.tutoringforall.org" target="_blank">tfa-calendar website</a>.
@endif
@component('mail::button', ['url' => url('/ml/') . '/' . $slot['id'], 'color' => 'primary'])
Meeting link
@endcomponent
@endcomponent

@component('mail::panel')
## What does student need help with?
{{ $slot['info'] }}
@endcomponent
@endcomponent