<?php

namespace App\Http\Controllers;

use App\Models\Slot;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class AjaxController extends Controller
{
    protected function get()
    {
        if (User::find(Auth::user()->id)->role() == 'tutor') {
            $output = [];
            foreach (Slot::select(['start', 'tutor_id', 'student_id', 'subject', 'info'])->where('tutor_id', Auth::user()->id)->get() as $item) {
                $temp = [];
                $extended = [];
                if (!is_null($item->student_id)) {
                    $studentname = User::select('name')->where('id', $item->student_id)->first()->name;
                    $temp['title'] = "Session with $studentname";
                    $temp['color'] = 'red';
                    $extended['studentname'] = $studentname;
                    $extended['studentemail'] = User::select('email')->where('id', $item->student_id)->first()->email;
                } else {
                    $temp['title'] = "Unclaimed session";
                }
                $extended['tutorname'] = Auth::user()->name;
                $temp['start'] = date("Y-m-d\\TH:i:s\\Z", strtotime($item->start));
                $temp['end'] = date("Y-m-d\\TH:i:s\\Z", strtotime($item->start . "+1 hours"));
                $temp['extendedProps'] = $extended;
                array_push($output, $temp);
            };
            return json_encode($output);
        } else if (User::find(Auth::user()->id)->role() == 'student') {
        }
    }
    protected function create(Request $request)
    {
        $request->validate([
            'start' => ['required', 'date', Rule::unique('slots', 'start')->where(function ($query) {
                return $query->where('tutor_id', Auth::user()->id);
            })]
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
            error_log('hello');
            Slot::create([
                'event_id' => uniqid(),
                'start' => $request->start,
                'tutor_id' => Auth::user()->id
            ]);
        }
    }
    protected function cancel(Request $request)
    {
        if (User::find(Auth::user()->id)->role() == 'tutor') {
            if (isset($request->studentname)) {
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
}
