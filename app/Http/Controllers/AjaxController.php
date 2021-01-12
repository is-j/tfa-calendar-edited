<?php

namespace App\Http\Controllers;

use App\Models\Slot;
use App\Models\User;
use App\Models\Tutor;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class AjaxController extends Controller
{
    protected function get()
    {
        date_default_timezone_set('UTC');
        $datefilter = date('Y-m-d H:i:s', strtotime('-2 hours'));
        $datebefore = date('Y-m-d H:i:s', strtotime('+6 hours'));
        if (User::find(Auth::user()->id)->role() == 'tutor') {
            $output = [];
            foreach (Slot::where('tutor_id', Auth::user()->id)->get() as $item) {
                $temp = [];
                $extended = [];
                if (is_null($item->student_id)) {
                    $temp['title'] = "Unclaimed session";
                    $temp['color'] = 'red';
                    $extended['claimed'] = false;
                } else {
                    $studentname = User::find($item->student_id)->name;
                    $temp['title'] = "Session with $studentname";
                    $extended['studentname'] = $studentname;
                    $extended['studentemail'] = User::find($item->student_id)->email;
                    $extended['subject'] = Subject::find($item->subject)->name;
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
            foreach (Slot::whereNull('student_id')->orWhere('student_id', Auth::user()->id)->get() as $item) {
                $temp = [];
                $extended = [];
                $tutorname = User::select('name')->where('id', $item->tutor_id)->first()->name;
                if (is_null($item->student_id) && $item->start >= $datebefore) {
                    $temp['title'] = "Unclaimed session";
                    $temp['color'] = 'red';
                    foreach (Tutor::find($item->tutor_id)->subjects() as $key) {
                        $extended['tutorsubjects'][$key] = Subject::find($key)->name;
                    }
                    $extended['claimed'] = false;
                    $extended['tutorname'] = $tutorname;
                    $extended['tutorbio'] = Tutor::find($item->tutor_id)->bio;
                    $temp['start'] = date("Y-m-d\\TH:i:s\\Z", strtotime($item->start));
                    $temp['end'] = date("Y-m-d\\TH:i:s\\Z", strtotime($item->start . "+1 hours"));
                    $temp['extendedProps'] = $extended;
                    array_push($output, $temp);
                } else if (!is_null($item->student_id) && $item->start >= $datefilter) {
                    $temp['title'] = "Session with $tutorname";
                    $extended['tutoremail'] = User::find($item->tutor_id)->email;
                    $extended['info'] = $item->info;
                    $extended['meeting_link'] = Tutor::find($item->tutor_id)->meeting_link;
                    error_log($item->subject);
                    $extended['subject'] = Subject::find($item->subject)->name;
                    $extended['claimed'] = true;
                    $extended['tutorname'] = $tutorname;
                    $extended['tutorbio'] = Tutor::find($item->tutor_id)->bio;
                    $temp['start'] = date("Y-m-d\\TH:i:s\\Z", strtotime($item->start));
                    $temp['end'] = date("Y-m-d\\TH:i:s\\Z", strtotime($item->start . "+1 hours"));
                    $temp['extendedProps'] = $extended;

                    error_log('hello');
                    array_push($output, $temp);
                }
            };
            return json_encode($output);
        }
    }
    protected function create(Request $request)
    {
        $request->validate([
            'start' => 'required|date|after:+2 hours'
        ]);
        if ($request->repeat == 'true') {
            for ($i = 0; $i < 20; $i++) {
                $start = date("Y-m-d H:i:s", strtotime($request->start . "+$i weeks"));
                if (!Slot::where('tutor_id', Auth::user()->id)->where('start', $start)->exists()) {
                    Slot::create([
                        'event_id' => uniqid(),
                        'start' => $start,
                        'tutor_id' => Auth::user()->id
                    ]);
                }
            }
        } else {
            if (!Slot::where('tutor_id', Auth::user()->id)->where('start', $request->start)->exists()) {
                Slot::create([
                    'event_id' => uniqid(),
                    'start' => $request->start,
                    'tutor_id' => Auth::user()->id
                ]);
            }
        }
    }
    protected function cancel(Request $request)
    {
        if (User::find(Auth::user()->id)->role() == 'tutor') {
            if (isset($request->studentname)) {
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
            'meeting_link' => 'required|url',
            'bio' => 'required|max:1000'
        ]);
        Tutor::where('user_id', Auth::user()->id)->update(['meeting_link' => $request->meeting_link, 'bio' => $request->bio]);
    }

    protected function claim(Request $request)
    {
        $tutorid = User::select('id')->where('name', $request->tutorname)->first()->id;
        $subjectid = Subject::select('id')->where('name', $request->subject)->first()->id;
        if (is_null(Slot::where('start', $request->start)->where('tutor_id', $tutorid)->first()->student_id)) {
            Slot::where('start', $request->start)->where('tutor_id', $tutorid)->update(['student_id' => Auth::user()->id, 'subject' => $subjectid, 'info' => $request->info]);
        }
    }
}
