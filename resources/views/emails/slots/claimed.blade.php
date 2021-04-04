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
The student {{ User::find($slot['student_id'])->name }} ({{ User::find($slot['student_id'])->email }}) claimed the session at {{ (new DateTime($slot['start']))->setTimezone(new DateTimeZone(User::find($slot['tutor_id'])->timezone))->format('D M j, Y g:i A') }} with you. Please remember to avoid canceling.
<br>
<br>
Please go to the following meeting link at least 5 minutes before {{ (new DateTime($slot['start']))->setTimezone(new DateTimeZone(User::find($slot['tutor_id'])->timezone))->format('D M j, Y g:i A') }}. If this is not your preferred meeting link, please change it on your profile on the <a href="https://cal.tutoringforall.org" target="_blank">tfa-calendar website</a> before the session.
@elseif ($role == 'student')
You claimed the session at {{ (new DateTime($slot['start']))->setTimezone(new DateTimeZone(User::find($slot['student_id'])->timezone))->format('D M j, Y g:i A') }} with the tutor {{ User::find($slot['tutor_id'])->name }} ({{ User::find($slot['tutor_id'])->email }}). Please remember to avoid canceling.
<br>
<br>
Please go to the following meeting link at least 5 minutes before {{ (new DateTime($slot['start']))->setTimezone(new DateTimeZone(User::find($slot['student_id'])->timezone))->format('D M j, Y g:i A') }}. You can also access all this information on the <a href="https://cal.tutoringforall.org" target="_blank">tfa-calendar website</a>.
@endif
@component('mail::button', ['url' => url('/ml/') . '/' . $slot['id'], 'color' => 'primary'])
Meeting link
@endcomponent
@endcomponent

@component('mail::panel')
## What does student need help with?
{{ $slot['info'] }}
@endcomponent
@component('mail::panel')
## This is a Do Not Reply email
@if ($role == 'tutor') 
No emails sent to scheduler@tutoringforall.org will be read. If you have any questions or information, please send it to your student or to info@tutoringforall.org.
@elseif ($role == 'student')
No emails sent to scheduler@tutoringforall.org will be read. If you have any questions or information, please send it to your tutor or to info@tutoringforall.org.
@endif
{{ $slot['info'] }}
@endcomponent
@endcomponent