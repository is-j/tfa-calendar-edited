@component('mail::message')
# Event claimed
@if ($roleName === 'tutor')
@component('mail::panel')
## General
The student {{ $event['student_name'] }} claimed the session at {{ $event['formatted_start'] }} with you. Please remember to avoid canceling.
<br>
<br>
Please go to the following meeting link at least 5 minutes before {{ $event['formatted_start'] }}. If this is not your preferred meeting link, please change it on your profile on the [Calendar website](https://cal.tutoringforall.org)</a> before the session.
@component('mail::button', ['url' => $event['meeting_link'], 'color' => 'primary'])
Meeting link
@endcomponent
@endcomponent
@component('mail::panel')
## What does your student need help with?
{{ $event['info'] }}
@endcomponent
@component('mail::panel')
## This is a Do Not Reply email
No emails sent to scheduler@tutoringforall.org will be read. If you have any questions or information, please send it to your student or to info@tutoringforall.org.
@endcomponent
@elseif ($roleName === 'student')
@component('mail::panel')
## General
You claimed the session at {{ $event['formatted_start'] }} with {{ $event['tutor_name'] }}. Please remember to avoid canceling.
<br>
<br>
Please go to the following meeting link at least 5 minutes before {{ $event['formatted_start'] }}. You can also access all this information on the [Calendar website](https://cal.tutoringforall.org)</a>.
@component('mail::button', ['url' => $event['meeting_link'], 'color' => 'primary'])
Meeting link
@endcomponent
@endcomponent
@component('mail::panel')
## What do you need help with?
{{ $event['info'] }}
@endcomponent
@component('mail::panel')
## This is a Do Not Reply email
No emails sent to scheduler@tutoringforall.org will be read. If you have any questions or information, please send it to your tutor or to info@tutoringforall.org.
@endcomponent

@endif
@endcomponent