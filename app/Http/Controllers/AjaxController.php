<?php

namespace App\Http\Controllers;


use App\Models\Role;
use App\Models\Slot;
use App\Models\User;
use App\Models\Tutor;
use App\Models\Report;
use App\Models\Subject;
use App\Jobs\ProcessBug;
use App\Models\Probation;
use App\Jobs\ProcessShare;
use App\Jobs\ProcessReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class AjaxController extends Controller
{
    public function get(Request $request, $id)
    {
        $interval = [date('Y-m-d H:i:s', strtotime($request['start'])), date('Y-m-d H:i:s', strtotime($request['end']))];
        $datefilter = date('Y-m-d H:i:s', strtotime('-2 hours'));
        $datebefore = date('Y-m-d H:i:s', strtotime('+6 hours'));
        $id = intval($id);
        if (User::find(Auth::user()->id)->role() == 'tutor') {
            $output = [];
            foreach (Slot::whereBetween('start', $interval)->where('tutor_id', Auth::user()->id)->get() as $item) {
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
                    $extended['meeting_link'] = url('/ml/') . '/' . $item->event_id;
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
            foreach (Slot::whereBetween('start', $interval)->where(function ($query) {
                $query->whereNull('student_id')->orWhere('student_id', Auth::user()->id);
            })->get() as $item) {
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
        } else if (User::find(Auth::user()->id)->role() == 'admin') {
            $output = [];
            foreach (Slot::get() as $item) {
                $temp = [];
                $extended = [];
                $extended['subject'] = Subject::find($item->subject)->name;
                $tutorname = User::find($item->tutor_id)->name;
                if (is_null($item->student_id)) {
                    $temp['title'] = "US: $tutorname";
                    $temp['color'] = 'red';
                    $extended['claimed'] = false;
                } else {
                    $studentname = User::find($item->student_id)->name;
                    $temp['title'] = "CS: $tutorname w/ $studentname";
                    $extended['studentname'] = $studentname;
                    $extended['info'] = $item->info;
                    $extended['claimed'] = true;
                }
                $temp['start'] = date("Y-m-d\\TH:i:s\\Z", strtotime($item->start));
                $temp['end'] = date("Y-m-d\\TH:i:s\\Z", strtotime($item->start . "+1 hours"));
                $temp['extendedProps'] = $extended;
                array_push($output, $temp);
            };
            return json_encode($output);
        }
    }
    protected function create(Request $request)
    {
        $request->validate([
            'start' => 'required|date|after:+6 hours',
            'subject' => 'required|integer'
        ]);
        if (!$this->underProbation()) {
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
        } else {
            return json_encode(['error' => true, 'msg' => 'You are under probation for canceling too often.']);
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
        }
        if (date('Y-m-d H:i:s', strtotime($request->start)) < date("Y-m-d H:i:s", strtotime('+2 hours'))) {
            DB::table(Role::find(Auth::user()->role_id)->name . 's')->where('user_id', Auth::user()->id)->increment('strikes');
            if (DB::table(Role::find(Auth::user()->role_id)->name . 's')->where('user_id', Auth::user()->id)->first()->strikes == 3) {
                if (Probation::where('user_id', Auth::user()->id)->exists) {
                    Probation::find(Auth::user()->id)->increment('history', 1, ['end' => date('Y-m-d H:i:s', strtotime('+1 week'))]);
                } else {
                    Probation::create(['user_id' => Auth::user()->id, 'end' => date('Y-m-d H:i:s', strtotime('+1 week'))]);
                }
                DB::table(Role::find(Auth::user()->role_id)->name . 's')->where('user_id', Auth::user()->id)->update(['strikes' => 0]);
            }
        }
        return json_encode(['success' => true]);
    }
    protected function getSubject()
    {
        $yh = [];
        $nh = [];
        foreach (Tutor::find(Auth::user()->id)->subjects() as $item) {
            array_push($yh, ['item' => $item, 'name' => Subject::find($item)->name]);
        }
        foreach (Subject::get() as $item) {
            if (!in_array($item->id, Tutor::find(Auth::user()->id)->subjects())) {
                array_push($nh, ['item' => $item->id, 'name' => $item->name]);
            }
        }
        return json_encode([$yh, $nh]);
    }
    protected function plusSubject(Request $request)
    {
        $subjects = Tutor::find(Auth::user()->id)->subjects();
        array_push($subjects, intval($request->subject));
        Tutor::where('user_id', Auth::user()->id)->update(['subjects' => json_encode(['subjects' => $subjects])]);
        return json_encode(['success' => true]);
    }
    protected function minusSubject(Request $request)
    {
        $subjects = Tutor::find(Auth::user()->id)->subjects();
        if (count($subjects) == 1) {
            return json_encode(['success' => false]);
        } else {
            $subjects = array_values(array_diff($subjects, [intval($request->subject)]));
            Tutor::where('user_id', Auth::user()->id)->update(['subjects' => json_encode(['subjects' => $subjects])]);
            return json_encode(['success' => true]);
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
        return json_encode(['success' => true]);
    }

    protected function claim(Request $request)
    {
        if (!$this->underProbation()) {
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
        } else {
            return json_encode(['error' => true, 'msg' => 'You are under probation for canceling too often.']);
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

    protected function underProbation()
    {
        $id = Auth::user()->id;
        if (Probation::where('user_id', $id)->exists()) {
            if (date('Y-m-d H:i:s', strtotime(Probation::find($id)->end)) > date('Y-m-d H:i:s')) {
                return true;
            } else {
                return false;
            }
        }
        return false;
    }

    protected function initReport()
    {
        if (User::find(Auth::user()->id)->role() != 'admin') {
            $output = ['exists' => false];
            $interval = [date('Y-m-d 00:00:00', strtotime('-1 day')), date('Y-m-d 23:59:59', strtotime('+1 day'))];
            if (Slot::where(User::find(Auth::user()->id)->role() . '_id', Auth::user()->id)->whereBetween('start', $interval)->exists()) {
                $output['exists'] = true;
                $output['starts'] = [];
                foreach (Slot::where(User::find(Auth::user()->id)->role() . '_id', Auth::user()->id)->whereBetween('start', $interval)->get() as $item) {
                    array_push($output['starts'], ['event_id' => $item->event_id, 'start' => $item->start]);
                }
            }
            return json_encode($output);
        }
    }

    protected function report(Request $request)
    {
        if ($request->type == 1) {
            $request->validate(['message' => 'required|max:1000']);
            ProcessBug::dispatch($request->message);
        } else {
            $reportedid = 0;
            if (Slot::find($request->event_id)->tutor_id == Auth::user()->id) {
                $reportedid = Slot::find($request->event_id)->student_id;
            } else {
                $reportedid = Slot::find($request->event_id)->tutor_id;
            }
            Report::create(['reporter_id' => Auth::user()->id, 'reported_id' => $reportedid, 'event_id' => $request->event_id, 'event_date' => Slot::find($request->event_id)->start]);
        }
        return json_encode(['success' => true]);
    }

    protected function confirmReport(Request $request)
    {
        $reportedid = Report::where('event_id', $request->event_id)->first()->reported_id;
        Report::where('event_id', $request->event_id)->delete();
        ProcessReport::dispatch($reportedid);
        return json_encode(['success' => true]);
    }

    protected function denyReport(Request $request)
    {
        Report::where('event_id', $request->event_id)->delete();
        return json_encode(['success' => true]);
    }
}
