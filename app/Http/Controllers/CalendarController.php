<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class CalendarController extends Controller
{
    protected function cancelSlot(Request $request)
    {
        return redirect('/cancel')->with('slot_id', $request->slot_id);
    }
}
