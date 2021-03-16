@php
use App\Models\User;
@endphp

@component('mail::message')
# You've been reported missing

@component('mail::panel')
# Slot at {{ (new DateTime($slotstart))->setTimezone(new DateTimeZone(User::find($id)->timezone))->format('D M j, Y g:i A') }}
You've been reported late (by more than 10 minutes) or absent to one of your tutoring sessions today. You've received one strike as a result. Three strikes result in a one week probation and lasting record on your account.
@endcomponent

@component('mail::panel')
# Think this was a mistake?
Within 3 days, please forward this email with an explanation of what happened to info@tutoringforall.org.
@endcomponent

@endcomponent