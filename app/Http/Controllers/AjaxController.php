<?php

namespace App\Http\Controllers;


use App\Models\Slot;
use App\Models\User;
use App\Models\Tutor;
use App\Models\Subject;
use App\Jobs\ProcessShare;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AjaxController extends Controller
{
    public function get($id)
    {
        date_default_timezone_set('UTC');
        $datefilter = date('Y-m-d H:i:s', strtotime('-2 hours'));
        $datebefore = date('Y-m-d H:i:s', strtotime('+6 hours'));
        $id = intval($id);
        if (User::find(Auth::user()->id)->role() == 'tutor') {
            $output = [];
            foreach (Slot::where('tutor_id', Auth::user()->id)->get() as $item) {
                $temp = [];
                $extended = [];
                $extended['subject'] = Subject::find($item->subject)->name;
                if (is_null($item->student_id)) {
                    $temp['title'] = "Unclaimed session";
                    $temp['color'] = 'red';
                    $extended['claimed'] = false;
                } else {
                    $studentname = User::find($item->student_id)->name;
                    $temp['title'] = "Session with $studentname";
                    $extended['studentname'] = $studentname;
                    $extended['studentemail'] = User::find($item->student_id)->email;
                    $extended['info'] = $item->info;
                    $extended['claimed'] = true;
                }
                $temp['start'] = date("Y-m-d\\TH:i:s\\Z", strtotime($item->start));
                $temp['end'] = date("Y-m-d\\TH:i:s\\Z", strtotime($item->start . "+1 hours"));
                $temp['extendedProps'] = $extended;
                array_push($output, $temp);
            };
            return json_encode($output);
        } else if (User::find(Auth::user()->id)->role() == 'student') {
            $output = [];
            $taken = [];
            foreach (Slot::where('student_id', Auth::user()->id)->get() as $item) {
                array_push($taken, date('Y-m-d H:i:s', strtotime($item->start)));
            }
            foreach (Slot::whereNull('student_id')->orWhere('student_id', Auth::user()->id)->get() as $item) {
                if ($id == 0 || $id == $item->subject) {
                    $temp = [];
                    $extended = [];
                    $tutorname = User::select('name')->where('id', $item->tutor_id)->first()->name;
                    if (is_null($item->student_id) && $item->start >= $datebefore && !in_array(date('Y-m-d H:i:s', strtotime($item->start)), $taken)) {
                        $temp['title'] = "Unclaimed session";
                        $temp['color'] = 'red';
                        $extended['claimed'] = false;
                        $extended['tutorname'] = $tutorname;
                        $extended['tutorbio'] = Tutor::find($item->tutor_id)->bio;
                        $extended['subject'] = Subject::find($item->subject)->name;
                        $temp['start'] = date("Y-m-d\\TH:i:s\\Z", strtotime($item->start));
                        $temp['end'] = date("Y-m-d\\TH:i:s\\Z", strtotime($item->start . "+1 hours"));
                        $temp['extendedProps'] = $extended;
                        array_push($output, $temp);
                    } else if (!is_null($item->student_id) && $item->start >= $datefilter) {
                        $temp['title'] = "Session with $tutorname";
                        $extended['tutoremail'] = User::find($item->tutor_id)->email;
                        $extended['info'] = $item->info;
                        $extended['meeting_link'] = url('/ml/') . '/' . $item->event_id;
                        $extended['subject'] = Subject::find($item->subject)->name;
                        $extended['claimed'] = true;
                        $extended['tutorname'] = $tutorname;
                        $extended['tutorbio'] = Tutor::find($item->tutor_id)->bio;
                        $temp['start'] = date("Y-m-d\\TH:i:s\\Z", strtotime($item->start));
                        $temp['end'] = date("Y-m-d\\TH:i:s\\Z", strtotime($item->start . "+1 hours"));
                        $temp['extendedProps'] = $extended;
                        array_push($output, $temp);
                    }
                }
            };
            return json_encode($output);
        }
    }
    protected function create(Request $request)
    {
        $request->validate([
            'start' => 'required|date|after:+2 hours',
            'subject' => 'required|integer'
        ]);
        if (Slot::where('tutor_id', Auth::user()->id)->where('start', $request->start)->exists()) {
            return json_encode(['error' => true, 'msg' => 'The slot already exists.']);
        }
        if ($request->repeat == 'true') {
            for ($i = 0; $i < 20; $i++) {
                $start = date("Y-m-d H:i:s", strtotime($request->start . "+$i weeks"));
                if (!Slot::where('tutor_id', Auth::user()->id)->where('start', $start)->exists()) {
                    Slot::create([
                        'event_id' => uniqid(),
                        'start' => $start,
                        'subject' => $request->subject,
                        'tutor_id' => Auth::user()->id
                    ]);
                }
            }
            return json_encode(['error' => false]);
        } else {
            Slot::create([
                'event_id' => uniqid(),
                'start' => $request->start,
                'subject' => $request->subject,
                'tutor_id' => Auth::user()->id
            ]);
            return json_encode(['error' => false]);
        }
    }
    protected function cancel(Request $request)
    {
        if (User::find(Auth::user()->id)->role() == 'tutor') {
            if (isset($request->name)) {
                $eventid = Slot::where('tutor_id', Auth::user()->id)->where('start', $request->start)->first()->event_id;
                ProcessShare::dispatch('delete', $eventid, ['tutor_id' => Slot::find($eventid)->tutor_id, 'student_id' => Slot::find($eventid)->student_id, 'start' => Slot::find($eventid)->start], $request->reason);
                Slot::where('tutor_id', Auth::user()->id)->where('start', $request->start)->delete();
            } else {
                if ($request->repeat == 'true') {
                    for ($i = 0; $i < 20; $i++) {
                        $start = date("Y-m-d H:i:s", strtotime($request->start . "+$i weeks"));
                        if (Slot::where('tutor_id', Auth::user()->id)->where('start', $start)->exists() && is_null(Slot::select('student_id')->where('tutor_id', Auth::user()->id)->where('start', $start)->first()->student_id)) {
                            Slot::where('tutor_id', Auth::user()->id)->where('start', $start)->delete();
                        }
                    }
                } else {
                    Slot::where('tutor_id', Auth::user()->id)->where('start', $request->start)->delete();
                }
            }
        } else if (User::find(Auth::user()->id)->role() == 'student') {
            $eventid = Slot::where('student_id', Auth::user()->id)->where('start', $request->start)->first()->event_id;
            ProcessShare::dispatch('unclaim', $eventid, ['tutor_id' => Slot::find($eventid)->tutor_id, 'student_id' => Slot::find($eventid)->student_id, 'start' => Slot::find($eventid)->start], $request->reason);
            Slot::where('student_id', Auth::user()->id)->where('start', $request->start)->update(['student_id' => NULL, 'info' => NULL]);
            if (date('Y-m-d H:i:s', strtotime($request->start)) < date('Y-m-d H:i:s', strtotime('+2 hours'))) {
                $date = date('Y-m-d H:i:s', strtotime($request->start));
            }
        }

    }
    protected function plusSubject(Request $request)
    {
        $subjects = Tutor::find(Auth::user()->id)->subjects();
        array_push($subjects, intval($request->subject));
        Tutor::where('user_id', Auth::user()->id)->update(['subjects' => json_encode(['subjects' => $subjects])]);
    }
    protected function minusSubject(Request $request)
    {
        $subjects = Tutor::find(Auth::user()->id)->subjects();
        if (count($subjects) == 1) {
            return false;
        } else {
            $subjects = array_values(array_diff($subjects, [intval($request->subject)]));
            Tutor::where('user_id', Auth::user()->id)->update(['subjects' => json_encode(['subjects' => $subjects])]);
            return true;
        }
    }
    protected function updateInformation(Request $request)
    {
        $request->validate([
            'meeting_link' => 'required',
            'bio' => 'required|max:1000'
        ]);
        $meetinglink = $request->meeting_link;
        if (!(strpos($meetinglink, 'http://') !== false || strpos($meetinglink, 'https://') !== false)) {
            $meetinglink = 'https://' . $meetinglink;
        }
        Tutor::where('user_id', Auth::user()->id)->update(['meeting_link' => $meetinglink, 'bio' => $request->bio]);
    }

    protected function claim(Request $request)
    {
        $tutorid = User::select('id')->where('name', $request->tutorname)->first()->id;
        if (is_null(Slot::where('start', $request->start)->where('tutor_id', $tutorid)->first()->student_id)) {
            Slot::where('start', $request->start)->where('tutor_id', $tutorid)->update(['student_id' => Auth::user()->id, 'info' => $request->info]);
            $eventid = Slot::where('student_id', Auth::user()->id)->where('start', $request->start)->first()->event_id;
            ProcessShare::dispatch('claim', $eventid, ['tutor_id' => Slot::find($eventid)->tutor_id, 'student_id' => Slot::find($eventid)->student_id, 'start' => Slot::find($eventid)->start], $request->info);
            $date = date('Y-m-d', strtotime($request->start));
            $time = date('H:i:s', strtotime($request->start));
            foreach (Slot::where('student_id', Auth::user()->id)->get() as $item) {
                if (date('Y-m-d', strtotime($item->start)) == $date && date('H:i:s', strtotime($item->start)) != $time) {
                    return json_encode(['error' => true, 'msg' => 'You have another slot the same day. Be mindful of the other students who also need tutoring.']);
                }
            }
            return json_encode(['error' => false]);
        }
    }

    protected function meetingLink($eventid)
    {
        if (Slot::where('event_id', $eventid)->exists()) {
            if (Auth::user()->id == Slot::find($eventid)->student_id) {
                $tutorid = Slot::find($eventid)->tutor_id;
                $meetinglink = Tutor::find($tutorid)->meeting_link;
                return redirect()->away($meetinglink);
            } else if (Auth::user()->id == Slot::find($eventid)->tutor_id) {
                $meetinglink = Tutor::find(Auth::user()->id)->meeting_link;
                return redirect()->away($meetinglink);
            } else {
                abort(403);
            }
        } else {
            abort(404);
        }
    }
}
