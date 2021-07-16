<?php

namespace App\Http\Controllers;

use DateTime;
use DateTimeZone;
use App\Models\User;
use Inertia\Inertia;
use App\Models\Event;
use DateTimeImmutable;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $nextEvent = $nextEventObject = null;
        $todayEvents = $todayEventObjects = [];
        $day = (new DateTimeImmutable())->setTimezone(new DateTimeZone(Auth::user()->timezone));
        $timeframe = [
            (new DateTimeImmutable($day->format('Y-m-d'), new DateTimeZone(Auth::user()->timezone)))->setTimezone(new DateTimeZone('UTC'))->format('Y-m-d H:i:s'),
            (new DateTimeImmutable($day->format('Y-m-d'), new DateTimeZone(Auth::user()->timezone)))->setTimezone(new DateTimeZone('UTC'))->modify('+1 days')->format('Y-m-d H:i:s'),
        ];
        if (Auth::user()->role->name === 'tutor') {
            if (Event::where('tutor_id', Auth::user()->id)->whereNotNull('student_id')->where('start', '>', (new DateTimeImmutable($day->format('Y-m-d'), new DateTimeZone(Auth::user()->timezone)))->setTimezone(new DateTimeZone('UTC'))->modify('+1 days')->format('Y-m-d H:i:s'))->exists()) {
                $nextEventObject = Event::where('tutor_id', Auth::user()->id)->whereNotNull('student_id')->where('start', '>', (new DateTimeImmutable($day->format('Y-m-d'), new DateTimeZone(Auth::user()->timezone)))->setTimezone(new DateTimeZone('UTC'))->modify('+1 days')->format('Y-m-d H:i:s'))->orderBy('start')->first();
            }
            $todayEventObjects = Event::where('tutor_id', Auth::user()->id)->whereNotNull('student_id')->whereBetween('start', $timeframe)->get();
        } else if (Auth::user()->role->name === 'student') {
            if (Event::where('student_id', Auth::user()->id)->where('start', '>', (new DateTimeImmutable($day->format('Y-m-d'), new DateTimeZone(Auth::user()->timezone)))->setTimezone(new DateTimeZone('UTC'))->modify('+1 days')->format('Y-m-d H:i:s'))->exists()) {
                $nextEventObject = Event::where('student_id', Auth::user()->id)->where('start', '>', (new DateTimeImmutable($day->format('Y-m-d'), new DateTimeZone(Auth::user()->timezone)))->setTimezone(new DateTimeZone('UTC'))->modify('+1 days')->format('Y-m-d H:i:s'))->orderBy('start')->first();
            }
            $todayEventObjects = Event::where('student_id', Auth::user()->id)->whereBetween('start', $timeframe)->get();
        }
        if (isset($nextEventObject)) {
            $nextEvent = [
                'id' => $nextEventObject->id,
                'start' => (new DateTimeImmutable($nextEventObject->start))->format(DateTime::ATOM),
                'subject_name' => Subject::where('id', $nextEventObject->subject_id)->first()->name,
                'tutor_name' => User::find($nextEventObject->tutor_id)->name,
                'student_name' => User::find($nextEventObject->student_id)->name,
            ];
        }
        foreach ($todayEventObjects as $event) {
            $outputEvent = [
                'id' => $event->id,
                'start' => (new DateTimeImmutable($event->start))->format(DateTime::ATOM),
                'subject_name' => Subject::where('id', $event->subject_id)->first()->name,
                'tutor_name' => User::find($event->tutor_id)->name,
                'student_name' => User::find($event->student_id)->name,
            ];
            array_push($todayEvents, $outputEvent);
        }
        return Inertia::render('Dashboard', [
            'nextEvent' => isset($nextEvent) ? $nextEvent : null,
            'todayEvents' => !empty($todayEvents) ? $todayEvents : null,
        ]);
    }
}
