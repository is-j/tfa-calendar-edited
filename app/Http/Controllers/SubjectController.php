<?php

namespace App\Http\Controllers;

use App\Models\Tutor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SubjectController extends Controller
{
    public function update(Request $request)
    {
        $selectedSubjects = [];
        foreach ($request->all() as $key => $value) {
            if ($value === true) {
                array_push($selectedSubjects, $key);
            }
        }
        if (count($selectedSubjects) === 0) {
            return back()->withErrors(['subjects' => 'At least one subject must be selected.']);
        } else {
            Tutor::where('user_id', Auth::user()->id)->update(['subjects' => json_encode($selectedSubjects)]);
            return back();
        }
    }
}
