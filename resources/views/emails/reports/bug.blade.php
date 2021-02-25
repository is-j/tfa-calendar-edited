@php
use App\Models\User;
@endphp
@component('mail::message')
# Bug report on {{ date("D M j, Y g:i A", strtotime("-6 hours")) }}
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