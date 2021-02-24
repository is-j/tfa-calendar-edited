@php
use App\Models\User;
@endphp

@component('mail::message')
# You've been reported missing

@component('mail::panel')
# Slot at {{ date("D M j, Y g:i A", strtotime($slotstart . sprintf("%+d",User::find($id)->offset * 60) . " minutes")) }}
You've been reported late (by more than 10 minutes) or absent to one of your tutoring sessions today. You've received one strike as a result. Three strikes result in a one week probation and lasting record on your account.
@endcomponent

@component('mail::panel')
# Think this was a mistake?
Please respond to this email within 3 days with an explanation of what happened.
@endcomponent

@endcomponent