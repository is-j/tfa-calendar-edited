<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;

use function PHPUnit\Framework\isFalse;

class CancelController extends Controller
{
    public function index(Request $request)
    {
        if ($request->session()->has('event')) {
            return Inertia::render('Cancel', [
                'event' => $request->session()->get('event'),
            ]);
        } else {
            $request->session()->now('alert_message', 'No event was selected.');
            return Inertia::render('Cancel');
        }
    }

    public function show(Request $request)
    {
        $request->session()->put('event', $request->event);
        return Inertia::render('Cancel', [
            'event' => $request->event,
        ]);
    }
}
