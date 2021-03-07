@php
use App\Models\User;
@endphp
@component('mail::message')
# Bug report on {{ (new DateTime)->setTimezone(new DateTimeZone('America/Chicago'))->format('D M j, Y g:i A') }}
@component('mail::panel')
## User
Id: {{ $userid }}
<br>
Name: {{ User::find($userid)->name }}
<br>
Email: {{ User::find($userid)->email }}
<br>
Role: {{ ucfirst(User::find($userid)->role->name) }}
@endcomponent
@component('mail::panel')
## Message
{{ $message }}
@endcomponent
@endcomponent