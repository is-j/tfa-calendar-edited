<?php

namespace App\Http\Controllers;

use DateTimeZone;
use Inertia\Inertia;
use App\Models\Event;
use DateTimeImmutable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $nextEvents = [];
        $todayEvents = [];
        // if (Auth::user()->role->name === 'tutor') {
        //     Events::where('tutor_id', Auth::user()->id)->where()->min('start')
        // } else if (Auth::user()->role->name === 'student') {
        // }
        $today = (new DateTimeImmutable())->setTimezone(new DateTimezone(Auth::user()->timezone));
        $timeframe = [
            $today->format('Y-m-d H:i:s'),
            $today->modify('+1 day')->format('Y-m-d H:i:s'),
        ];
        return dd(Event::where('tutor_id', Auth::user()->id)->whereBetween('start', $timeframe)->get());
        return Inertia::render('Dashboard', [
            'nextEvents' => $nextEvents,
            'todayEvents' => $todayEvents,
        ]);
    }
}
