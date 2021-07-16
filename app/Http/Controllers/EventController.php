<?php

namespace App\Http\Controllers;

use App\Mail\EventCanceled;
use DateTime;
use DateTimeZone;
use App\Models\User;
use App\Models\Event;
use App\Models\Tutor;
use DateTimeImmutable;
use App\Models\Subject;
use App\Models\Language;
use App\Mail\EventClaimed;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class EventController extends Controller
{
    public function index(Request $request)
    {
        $timeframe = [
            (new DateTimeImmutable($request['start']))->format('Y-m-d H:i:s'),
            (new DateTimeImmutable($request['end']))->format('Y-m-d H:i:s'),
        ];
        $allEvents = [];
        if (Auth::user()->role->name === 'tutor') {
            foreach (Event::whereBetween('start', $timeframe)->where('tutor_id', Auth::user()->id)->get() as $event) {
                $outputEvent = [
                    'id' => $event->id,
                    'start' => (new DateTimeImmutable($event->start))->format(DateTime::ATOM),
                    'end' => (new DateTimeImmutable($event->start))->modify('+1 hours')->format(DateTime::ATOM),
                ];
                if (is_null($event->student_id)) {
                    $outputEvent['title'] = 'Unclaimed event';
                    $outputEvent['color'] = 'red';
                } else {
                    $outputEvent['title'] = User::find($event->student_id)->name;
                    $outputEvent['color'] = 'blue';
                }
                array_push($allEvents, $outputEvent);
            };
        } else if (Auth::user()->role->name === 'student') {
            foreach (Event::whereBetween('start', $timeframe)->where(function ($query) {
                $query->where('student_id', Auth::user()->id)
                    ->orWhereNull('student_id');
            })->get() as $event) {
                $outputEvent = [
                    'id' => $event->id,
                    'start' => (new DateTimeImmutable($event->start))->format(DateTime::ATOM),
                    'end' => (new DateTimeImmutable($event->start))->modify('+1 hours')->format(DateTime::ATOM),
                ];
                if (is_null($event->student_id)) {
                    $outputEvent['title'] = 'Unclaimed event';
                    $outputEvent['color'] = 'red';
                } else {
                    $outputEvent['title'] = User::find($event->tutor_id)->name;
                    $outputEvent['color'] = 'blue';
                }
                array_push($allEvents, $outputEvent);
            };
        }
        return json_encode($allEvents);
    }

    public function store(Request $request)
    {
        $request->validate([
            'start' => 'required|date|after:+6 hours',
            'subject_id' => 'required',
            'repeat' => 'required|boolean',
        ]);
        if (Event::where('tutor_id', Auth::user()->id)->where('start', $request->start)->exists()) {
            $request->session()->flash('alert_message', 'Action canceled. Event already exists.');
            return back();
        }
        if ($request->repeat) {
            $start = new DateTimeImmutable($request->start);
            for ($i = 0; $i < 20; $i++) {
                $nextStart = $start->modify('+' . $i . ' weeks')->format('Y-m-d H:i:s');
                if (!Event::where('tutor_id', Auth::user()->id)->where('start', $nextStart)->exists()) {
                    Event::create([
                        'id' => uniqid(),
                        'start' => $nextStart,
                        'subject_id' => intval($request->subject_id),
                        'tutor_id' => Auth::user()->id,
                    ]);
                }
            }
        } else {
            function findUniqueEventId()
            {
                $id = uniqid();
                if (Event::where('id', $id)->exists()) {
                    return findUniqueEventId();
                } else {
                    return $id;
                }
            }
            Event::create([
                'id' => findUniqueEventId(),
                'start' => $request->start,
                'subject_id' => intval($request->subject_id),
                'tutor_id' => Auth::user()->id,
            ]);
        }
        return back();
    }

    public function show($id)
    {
        $event = Event::find($id);
        $allLanguages = Language::select('id', 'name')->get();
        $selectedLanguageIds = json_decode(Tutor::where('user_id', $event->tutor_id)->first()->languages);
        $selectedLanguageNames = [];
        foreach ($allLanguages as $language) {
            if (in_array($language['id'], $selectedLanguageIds)) {
                array_push($selectedLanguageNames, $language['name']);
            }
        }
        $eventProps = [
            'id' => $event->id,
            'start' => (new DateTimeImmutable($event->start))->format(DateTime::ATOM),
            'subject_name' => Subject::where('id', $event->subject_id)->first()->name,
            'tutor_name' => User::find($event->tutor_id)->name,
            'tutor_email' => User::find($event->tutor_id)->email,
            'tutor_languages' => $selectedLanguageNames,
            'student_name' => isset($event->student_id) ? User::find($event->student_id)->name : null,
            'student_email' => isset($event->student_id) ? User::find($event->student_id)->email : null,
            'info' => isset($event->info) ? $event->info : null,
            'meeting_link' => '/ml/' . $event->id,
        ];
        return json_encode($eventProps);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'info' => 'required|max:1000',
        ]);
        $event = Event::find($id);
        if (is_null($event->student_id)) {
            Event::where('id', $id)->update(['student_id' => Auth::user()->id, 'info' => $request->info]);
            $event->refresh();
            foreach ([$event->tutor_id, $event->student_id] as $userId) {
                Mail::to(User::find($userId)->email)->queue(new EventClaimed($userId, $id));
            }
            $day = (new DateTimeImmutable($event->start))->setTimezone(new DateTimeZone(Auth::user()->timezone));
            $timeframe = [
                (new DateTimeImmutable($day->format('Y-m-d'), new DateTimeZone(Auth::user()->timezone)))->setTimezone(new DateTimeZone('UTC'))->format('Y-m-d H:i:s'),
                (new DateTimeImmutable($day->format('Y-m-d'), new DateTimeZone(Auth::user()->timezone)))->setTimezone(new DateTimeZone('UTC'))->modify('+1 days')->format('Y-m-d H:i:s'),
            ];
            if (Event::where('student_id', Auth::user()->id)->whereBetween('start', $timeframe)->count() > 1) {
                $request->session()->flash('alert_message', 'You have another session the same day. Please be mindful of the other students who need tutoring.');
            } else {
                $request->session()->flash('alert_message', 'Event claimed! Please check your email for more details.');
            }
            return back();
        }
    }

    public function destroy(Request $request, $id)
    {
        $event = Event::find($id);
        if (Auth::user()->role->name === 'tutor') {
            if (is_null($event->student_id)) {
                $request->validate([
                    'repeat' => 'required|boolean',
                ]);
                if ($request->repeat) {
                    $start = new DateTimeImmutable(Event::find($id)->start);
                    for ($i = 0; $i < 20; $i++) {
                        $nextStart = $start->modify('+' . $i . ' weeks')->format('Y-m-d H:i:s');
                        if (Event::where('tutor_id', Auth::user()->id)->where('start', $nextStart)->whereNull('student_id')->exists()) {
                            Event::where('tutor_id', Auth::user()->id)->where('start', $nextStart)->delete();
                        }
                    }
                } else {
                    Event::where('id', $id)->delete();
                }
            } else {
                $request->validate([
                    'reason' => 'required|max:1000',
                ]);
                foreach ([$event->tutor_id, $event->student_id] as $userId) {
                    Mail::to(User::find($userId)->email)->queue(new EventCanceled($userId, $id, [
                        'reason' => $request->reason,
                        'responsible' => 'tutor',
                    ]));
                }
                Event::where('id', $id)->delete();
                $request->session()->forget('event');
                $request->session()->flash('alert_message', 'Event canceled. Please remember to avoid doing so in the future.');
            }
        } else if (Auth::user()->role->name === 'student') {
            $request->validate([
                'reason' => 'required|max:1000',
            ]);
            foreach ([$event->tutor_id, $event->student_id] as $userId) {
                Mail::to(User::find($userId)->email)->queue(new EventCanceled($userId, $id, [
                    'reason' => $request->reason,
                    'responsible' => 'tutor',
                ]));
            }
            if (Auth::user()->id === $event->student_id) {
                Event::where('id', $id)->update(['student_id' => NULL, 'info' => NULL]);
            }
            $request->session()->forget('event');
            $request->session()->flash('alert_message', 'Event canceled. Please remember to avoid doing so in the future.');
        }
        return redirect('/calendar');
    }
}
