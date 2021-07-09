<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Tutor;
use Illuminate\Http\Request;

class MeetingLinkController extends Controller
{
    public function index($id)
    {
        $eventTutorId = Event::find($id)->tutor_id;
        $meetingLink = Tutor::where('user_id', $eventTutorId)->first()->meeting_link;
        return redirect($meetingLink);
    }
}
