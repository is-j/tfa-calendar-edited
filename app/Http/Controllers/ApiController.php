<?php

namespace App\Http\Controllers;

use DateTime;
use DateTimeZone;
use App\Models\Role;
use App\Models\Slot;
use App\Models\User;
use App\Models\Tutor;
use App\Models\Report;
use App\Mail\ReportBug;
use App\Models\Subject;
use App\Models\Language;
use App\Jobs\ProcessSlot;
use App\Mail\SlotClaimed;
use App\Models\Probation;
use App\Mail\ReportPerson;
use App\Mail\SlotCanceled;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class ApiController extends Controller
{
    protected function getSlot(Request $request, $subjectid)
    {
        $subjectid = intval($subjectid);
        $dtinterval = [date('Y-m-d H:i:s', strtotime($request['start'])), date('Y-m-d H:i:s', strtotime($request['end']))];
        $dtbefore = date('Y-m-d H:i:s', strtotime('-2 hours'));
        $dtafter = date('Y-m-d H:i:s', strtotime('+6 hours'));
        if (Auth::user()->role->name == 'tutor') {
            $slots = [];
            $dtinterval[0] = (new DateTime(date('Y-m-d')))->setTimezone(new DateTimeZone(Auth::user()->timezone))->format('Y-m-d H:i:s');
            foreach (Slot::whereBetween('start', $dtinterval)->where('tutor_id', Auth::user()->id)->get() as $slot) {
                $main = [];
                $extended = [];
                if (is_null($slot->student_id)) {
                    $main['title'] = "Unclaimed session";
                    $main['color'] = 'red';
                    $extended['claimed'] = false;
                } else {
                    $studentname = User::find($slot->student_id)->name;
                    $main['title'] = "$studentname";
                    $extended['student_name'] = $studentname;
                    $extended['student_email'] = User::find($slot->student_id)->email;
                    $extended['info'] = $slot->info;
                    $extended['meeting_link'] = url('/ml/') . '/' . $slot->id;
                    $extended['claimed'] = true;
                }
                $extended['subject_name'] = Subject::find($slot->subject_id)->name;
                $main['id'] = $slot->id;
                $main['start'] = date("Y-m-d\\TH:i:s\\Z", strtotime($slot->start));
                $main['end'] = date("Y-m-d\\TH:i:s\\Z", strtotime($slot->start . "+1 hours"));
                $main['extendedProps'] = $extended;
                array_push($slots, $main);
            };
            return json_encode($slots);
        } else if (Auth::user()->role->name == 'student') {
            $slots = [];
            $closed = [];
            foreach (Slot::whereBetween('start', $dtinterval)->where('student_id', Auth::user()->id)->get() as $slot) {
                array_push($closed, date('Y-m-d H:i:s', strtotime($slot->start)));
            }
            foreach (Slot::whereBetween('start', $dtinterval)->where(function ($query) {
                $query->whereNull('student_id')->orWhere('student_id', Auth::user()->id);
            })->get() as $slot) {
                if (($subjectid == 0 || $subjectid == $slot->subject_id)) {
                    $main = [];
                    $extended = [];
                    $tutorname = User::find($slot->tutor_id)->name;
                    if (is_null($slot->student_id) && $slot->start >= $dtafter && !in_array(date('Y-m-d H:i:s', strtotime($slot->start)), $closed) && !$this->isSuspended($slot->tutor_id)) {
                        $main['title'] = "$tutorname session";
                        $main['color'] = 'red';
                        $extended['claimed'] = false;
                        $extended['tutor_name'] = $tutorname;
                        $extended['tutor_bio'] = User::find($slot->tutor_id)->tutor->bio;
                        $extended['tutor_languages'] = [];
                        foreach (json_decode(User::find($slot->tutor_id)->tutor->languages) as $languageid) {
                            array_push($extended['tutor_languages'], Language::find($languageid)->name);
                        }
                        $extended['subject_name'] = Subject::find($slot->subject_id)->name;
                        $main['id'] = $slot->id;
                        $main['start'] = date("Y-m-d\\TH:i:s\\Z", strtotime($slot->start));
                        $main['end'] = date("Y-m-d\\TH:i:s\\Z", strtotime($slot->start . "+1 hours"));
                        $main['extendedProps'] = $extended;
                        array_push($slots, $main);
                    } else if (!is_null($slot->student_id) && $slot->start >= $dtbefore) {
                        $main['title'] = "$tutorname session";
                        $extended['tutor_name'] = $tutorname;
                        $extended['tutor_bio'] = User::find($slot->tutor_id)->tutor->bio;
                        $extended['tutor_email'] = User::find($slot->tutor_id)->email;
                        $extended['tutor_languages'] = [];
                        foreach (json_decode(User::find($slot->tutor_id)->tutor->languages) as $languageid) {
                            array_push($extended['tutor_languages'], Language::find($languageid)->name);
                        }
                        $extended['info'] = $slot->info;
                        $extended['meeting_link'] = url('/ml/') . '/' . $slot->id;
                        $extended['subject_name'] = Subject::find($slot->subject_id)->name;
                        $extended['claimed'] = true;
                        $main['id'] = $slot->id;
                        $main['start'] = date("Y-m-d\\TH:i:s\\Z", strtotime($slot->start));
                        $main['end'] = date("Y-m-d\\TH:i:s\\Z", strtotime($slot->start . "+1 hours"));
                        $main['extendedProps'] = $extended;
                        array_push($slots, $main);
                    }
                }
            };
            return json_encode($slots);
        } else if (Auth::user()->role->name == 'admin') {
            $slots = [];
            foreach (Slot::whereBetween('start', $dtinterval)->get() as $slot) {
                $main = [];
                $extended = [];
                $extended['subject'] = Subject::find($slot->subject_id)->name;
                $tutorname = User::find($slot->tutor_id)->name;
                if (is_null($slot->student_id)) {
                    $main['title'] = "US: $tutorname";
                    $main['color'] = 'red';
                    $extended['claimed'] = false;
                } else {
                    $studentname = User::find($slot->student_id)->name;
                    $main['title'] = "CS: $tutorname w/ $studentname";
                    $extended['studentname'] = $studentname;
                    $extended['info'] = $slot->info;
                    $extended['claimed'] = true;
                }
                $main['start'] = date("Y-m-d\\TH:i:s\\Z", strtotime($slot->start));
                $main['end'] = date("Y-m-d\\TH:i:s\\Z", strtotime($slot->start . "+1 hours"));
                $main['extendedProps'] = $extended;
                array_push($slots, $main);
            };
            return json_encode($slots);
        }
    }

    protected function createSlot(Request $request)
    {
        $catchProbation = $this->catchProbation();
        if ($catchProbation['probation']) {
            return json_encode($catchProbation['content']);
        }
        $request->validate([
            'start' => 'required|date|after:+6 hours',
            'subject_id' => 'required',
            'repeat' => 'required|boolean'
        ]);
        if (Slot::where('tutor_id', Auth::user()->id)->where('start', $request->start)->exists()) {
            return json_encode(['success' => false, 'error' => true, 'message' => 'The slot already exists.']);
        }
        if ($request->repeat == true) {
            for ($i = 0; $i < 20; $i++) {
                $start = date("Y-m-d H:i:s", strtotime($request->start . "+$i weeks"));
                if (!Slot::where('tutor_id', Auth::user()->id)->where('start', $start)->exists()) {
                    Slot::create([
                        'id' => uniqid(),
                        'start' => $start,
                        'subject_id' => intval($request->subject_id),
                        'tutor_id' => Auth::user()->id
                    ]);
                }
            }
            return json_encode(['success' => true, 'error' => false]);
        } else {
            Slot::create([
                'id' => uniqid(),
                'start' => $request->start,
                'subject_id' => intval($request->subject_id),
                'tutor_id' => Auth::user()->id
            ]);
            return json_encode(['success' => true, 'error' => false]);
        }
    }

    protected function cancelSlot(Request $request)
    {
        $catchProbation = $this->catchProbation();
        if ($catchProbation['probation']) {
            return json_encode($catchProbation['content']);
        }
        $storedstart = Slot::find($request->id)->start;
        if (Auth::user()->role->name == 'tutor') {
            if (is_null(Slot::find($request->id)->student_id)) {
                if ($request->repeat) {
                    $start = Slot::find($request->id)->start;
                    for ($i = 0; $i < 20; $i++) {
                        if (Slot::where('tutor_id', Auth::user()->id)->where('start', $start)->whereNull('student_id')->exists()) {
                            Slot::where('tutor_id', Auth::user()->id)->where('start', $start)->delete();
                        }
                        $start = date("Y-m-d H:i:s", strtotime($start . '+1 week'));
                    }
                } else {
                    Slot::where('id', $request->id)->delete();
                }
            } else {
                $slot = Slot::find($request->id)->toArray();
                ProcessSlot::dispatch('cancel', $slot);
                Mail::to(User::find($slot['tutor_id']))->queue(new SlotCanceled($slot, 'tutor', 'delete', $request->reason));
                Mail::to(User::find($slot['student_id']))->queue(new SlotCanceled($slot, 'student', 'delete', $request->reason));
                Slot::where('id', $request->id)->delete();
            }
        } else if (Auth::user()->role->name == 'student') {
            $slot = Slot::find($request->id)->toArray();
            ProcessSlot::dispatch('cancel', $slot);
            Mail::to(User::find($slot['tutor_id']))->queue(new SlotCanceled($slot, 'tutor', 'unclaim', $request->reason));
            Mail::to(User::find($slot['student_id']))->queue(new SlotCanceled($slot, 'student', 'unclaim', $request->reason));
            Slot::where('id', $request->id)->update(['student_id' => NULL, 'info' => NULL]);
        }
        if (date('Y-m-d H:i:s', strtotime($storedstart)) < date("Y-m-d H:i:s", strtotime('+2 hours'))) {
            $this->addStrike(Auth::user()->id);
            return json_encode(['success' => true, 'error' => true, 'message' => "You've received a strike for canceling near the start of the session. 3 strikes result in a probation of 1 week."]);
        }
        return json_encode(['success' => true, 'error' => false]);
    }

    protected function claimSlot(Request $request)
    {
        $catchProbation = $this->catchProbation();
        if ($catchProbation['probation']) {
            return json_encode($catchProbation['content']);
        }
        $request->validate([
            'id' => 'required',
            'info' => 'required|max:1000'
        ]);
        if (is_null(Slot::find($request->id)->student_id)) {
            Slot::where('id', $request->id)->update(['student_id' => Auth::user()->id, 'info' => $request->info]);
            $slot = Slot::find($request->id)->toArray();
            ProcessSlot::dispatch('claim', $slot);
            Mail::to(User::find($slot['tutor_id']))->queue(new SlotClaimed($slot, 'tutor'));
            Mail::to(User::find($slot['student_id']))->queue(new SlotClaimed($slot, 'student'));
            $dtinterval = [(new DateTime(date('Y-m-d', strtotime(Slot::find($request->id)->start))))->setTimezone(new DateTimeZone(Auth::user()->timezone))->format('Y-m-d H:i:s'), (new DateTime(date('Y-m-d', strtotime(Slot::find($request->id)->start . '+1 day'))))->setTimezone(new DateTimeZone(Auth::user()->timezone))->format('Y-m-d H:i:s')];
            if (Slot::where('student_id', Auth::user()->id)->where('start', '>=', $dtinterval[0])->where('start', '<', $dtinterval[1])->count() > 1) {
                return json_encode(['success' => true, 'error' => true, 'message' => 'You have another session the same day. Be mindful of the other students who also need tutoring.']);
            }
            return json_encode(['success' => true, 'error' => false]);
        }
    }

    protected function getSubjectAll()
    {
        return json_encode(Subject::get());
    }

    protected function createSubject(Request $request)
    {
        if (!Subject::where('name', $request->subject_name)->exists()) {
            Subject::create(['name' => $request->subject_name]);
            return json_encode(['success' => true]);
        }
        return json_encode(['success' => false]);
    }

    protected function getSubject()
    {
        $subjects = json_decode(Auth::user()->tutor->subjects);
        $yh = [];
        $nh = [];
        foreach ($subjects as $subject) {
            array_push($yh, ['id' => $subject, 'name' => Subject::find($subject)->name]);
        }
        foreach (Subject::get() as $subject) {
            if (!in_array($subject->id, $subjects)) {
                array_push($nh, ['id' => $subject->id, 'name' => $subject->name]);
            }
        }
        return json_encode([$yh, $nh]);
    }

    protected function plusSubject(Request $request)
    {
        $subjects = json_decode(Auth::user()->tutor->subjects);
        array_push($subjects, intval($request->subject_id));
        Tutor::where('user_id', Auth::user()->id)->update(['subjects' => json_encode($subjects)]);
        return json_encode(['success' => true]);
    }

    protected function minusSubject(Request $request)
    {
        $subjects = json_decode(Auth::user()->tutor->subjects);
        if (count($subjects) == 1) {
            return json_encode(['success' => false]);
        } else {
            $subjects = array_values(array_diff($subjects, [intval($request->subject_id)]));
            Tutor::where('user_id', Auth::user()->id)->update(['subjects' => json_encode($subjects)]);
            return json_encode(['success' => true]);
        }
    }

    protected function updateInformation(Request $request)
    {
        $request->validate([
            'meeting_link' => 'required',
            'bio' => 'required|max:1000',
            'languages' => 'required'
        ]);
        $meetingLink = $request->meeting_link;
        if (!(strpos($meetingLink, 'http://') !== false || strpos($meetingLink, 'https://') !== false)) {
            $meetingLink = 'https://' . $meetingLink;
        }
        Tutor::where('user_id', Auth::user()->id)->update(['meeting_link' => $meetingLink, 'bio' => $request->bio, 'languages' => json_encode($request->languages)]);
        return json_encode(['success' => true]);
    }

    protected function initReport()
    {
        if (Auth::user()->role->name != 'admin') {
            $output = ['exists' => false];
            $dtinterval = [(new DateTime(date('Y-m-d')))->setTimezone(new DateTimeZone(Auth::user()->timezone))->format('Y-m-d H:i:s'), (new DateTime(date('Y-m-d', strtotime('+1 day'))))->setTimezone(new DateTimeZone(Auth::user()->timezone))->format('Y-m-d H:i:s')];
            $roles = [];
            if (Auth::user()->role->name == 'tutor') {
                $roles = ['tutor_id', 'student_id'];
            } else if (Auth::user()->role->name == 'student') {
                $roles = ['student_id', 'tutor_id'];
            }
            if (Slot::where($roles[0], Auth::user()->id)->whereNotNull($roles[1])->where('start', '>=', $dtinterval[0])->where('start', '<', $dtinterval[1])->exists()) {
                $output['slots'] = [];
                foreach (Slot::where($roles[0], Auth::user()->id)->whereNotNull($roles[1])->where('start', '>=', $dtinterval[0])->where('start', '<', $dtinterval[1])->get() as $slot) {
                    if (!Report::where('slot_id', $slot->id)->exists()) {
                        array_push($output['slots'], ['id' => $slot->id, 'start' => $slot->start]);
                    }
                }
                if (count($output['slots']) == 0) {
                    $output['success'] = false;
                } else {
                    $output['success'] = true;
                }
            }
            return json_encode($output);
        }
        return json_encode(['success' => false]);
    }

    protected function sendReport(Request $request)
    {
        if ($request->type == 1) {
            Mail::to(User::find(1))->queue(new ReportBug(Auth::user()->id, $request->message));
        } else if ($request->type == 2 && !Report::where('slot_id', $request->slot_id)->exists()) {
            $catchProbation = $this->catchProbation();
            if ($catchProbation['probation']) {
                return json_encode($catchProbation['content']);
            }
            if (Auth::user()->role->name == 'tutor') {
                Report::create([
                    'reporter_id' => Auth::user()->id,
                    'reported_id' => Slot::find($request->slot_id)->student_id,
                    'slot_id' => $request->slot_id,
                    'slot_start' => Slot::find($request->slot_id)->start
                ]);
            } else if (Auth::user()->role->name == 'student') {
                Report::create([
                    'reporter_id' => Auth::user()->id,
                    'reported_id' => Slot::find($request->slot_id)->tutor_id,
                    'slot_id' => $request->slot_id,
                    'slot_start' => Slot::find($request->slot_id)->start
                ]);
            }
        }
        return json_encode(['success' => true, 'error' => false]);
    }

    protected function getReport()
    {
        $output = [];
        foreach (Report::get() as $report) {
            array_push($output, ['reporter_info' => User::find($report->reporter_id)->name . ' - ' . ucfirst(User::find($report->reporter_id)->role->name), 'reporter_email' => User::find($report->reporter_id)->email, 'reported_info' => User::find($report->reported_id)->name . ' - ' . ucfirst(User::find($report->reported_id)->role->name), 'reported_email' => User::find($report->reported_id)->email, 'slot_start' => $report->slot_start, 'created_at' => date('Y-m-d H:i:s', strtotime($report->created_at)), 'slot_id' => $report->slot_id, 'confirmed' => $report->confirmed]);
        }
        return json_encode(array_reverse($output));
    }

    protected function confirmReport(Request $request)
    {
        $report = Report::where('slot_id', $request->slot_id)->first();
        $this->addStrike($report->reported_id);
        Mail::to(User::find($report->reported_id))->queue(new ReportPerson($report->reported_id, $report->slot_start));
        Report::where('slot_id', $request->slot_id)->update(['confirmed' => true]);
        return json_encode(['success' => true]);
    }

    protected function denyReport(Request $request)
    {
        Report::where('slot_id', $request->slot_id)->update(['confirmed' => false]);
        return json_encode(['success' => true]);
    }

    protected function redirectMeetingLink($slotid)
    {
        if (Slot::find($slotid)->exists()) {
            if (Auth::user()->id == Slot::find($slotid)->student_id) {
                $tutorid = Slot::find($slotid)->tutor_id;
                $meetinglink = User::find($tutorid)->tutor->meeting_link;
                return redirect()->away($meetinglink);
            } else if (Auth::user()->id == Slot::find($slotid)->tutor_id) {
                $meetinglink = Auth::user()->tutor->meeting_link;
                return redirect()->away($meetinglink);
            } else {
                abort(403);
            }
        } else {
            abort(404);
        }
    }

    protected function getUser()
    {
        $output = [];
        foreach (User::get() as $user) {
            if ($user->role->name != 'admin') {
                $status = 'Active';
                if (!DB::table($user->role->name . 's')->where('user_id', $user->id)->exists()) {
                    $status = 'Not setup';
                } else if ($this->isSuspended($user->id)) {
                    $status = 'Probation';
                }
                $probation = '';
                if (is_null($user->probation)) {
                    $probation = NULL;
                } else {
                    $probation = $user->probation->history;
                }
                array_push($output, ['name' => $user->name, 'email' => $user->email, 'probation' => $probation, 'strikes' => $user->strikes, 'status' => $status, 'role' => ucfirst($user->role->name)]);
            }
        }
        return json_encode($output);
    }

    protected function addStrike($id)
    {
        User::find($id)->increment('strikes');
        if (User::find($id)->strikes == 3) {
            if (is_null(User::find($id)->probation)) {
                Probation::create(['user_id' => $id, 'end' => date('Y-m-d H:i:s', strtotime('+1 week'))]);
            } else {
                Probation::where('user_id', $id)->increment('history', 1, ['end' => date('Y-m-d H:i:s', strtotime('+1 week'))]);
            }
            User::where('id', $id)->update(['strikes' => 0]);
        }
    }

    protected function isSuspended($id)
    {
        return (Probation::where('user_id', $id)->exists() && date('Y-m-d H:i:s', strtotime(Probation::where('user_id', $id)->first()->end)) > date('Y-m-d H:i:s'));
    }

    protected function catchProbation()
    {
        if ($this->isSuspended(Auth::user()->id)) {
            $end = (new DateTime(Probation::where('user_id', Auth::user()->id)->first()->end))->setTimezone(new DateTimeZone(Auth::user()->timezone))->format('D M j, Y g:i A');
            return ['probation' => true, 'content' => ['success' => false, 'error' => true, 'message' => "Your account is suspended for too many strikes and cannot do the requested action. Your probation ends $end."]];
        }
        return ['probation' => false];
    }
}
