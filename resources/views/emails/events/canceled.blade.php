@component('mail::message')
# Event canceled
@if ($roleName === 'tutor')
@component('mail::panel')
## General
@if ($event['responsible'] === 'tutor')
You canceled the session at {{ $event['formatted_start'] }} with {{ $event['student_name'] }}. Please remember to avoid canceling in the future.
@elseif ($event['responsible'] === 'student')
The student {{ $event['student_name'] }} canceled the session at {{ $event['formatted_start'] }} with you. We apologize for the inconvenience.
@endif
<br>
<br>
All of these changes are visible on the [Calendar website](https://cal.tutoringforall.org)</a>.
@endcomponent
@component('mail::panel')
## Cancellation reason
{{ $event['reason'] }}
@endcomponent
@component('mail::panel')
## This is a Do Not Reply email
No emails sent to scheduler@tutoringforall.org will be read. If you have any questions or information, please send it to your student or to info@tutoringforall.org.
@endcomponent
@elseif ($roleName === 'student')
@component('mail::panel')
## General
@if ($event['responsible'] === 'tutor')
You canceled the session at {{ $event['formatted_start'] }} with {{ $event['tutor_name'] }}. Please remember to avoid canceling in the future.
@elseif ($event['responsible'] === 'student')
The tutor {{ $event['tutor_name'] }} canceled the session at {{ $event['formatted_start'] }} with you. We apologize for the inconvenience.
@endif
<br>
<br>
All of these changes are visible on the [Calendar website](https://cal.tutoringforall.org)</a>.
@endcomponent
@component('mail::panel')
## Cancellation reason
{{ $event['reason'] }}
@endcomponent
@component('mail::panel')
## This is a Do Not Reply email
No emails sent to scheduler@tutoringforall.org will be read. If you have any questions or information, please send it to your tutor or to info@tutoringforall.org.
@endcomponent

@endif
@endcomponent