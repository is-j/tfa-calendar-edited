<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

class CalendarController extends Controller
{
    public function index()
    {
        if (Auth::user()->role->name === 'tutor') {
            $allSubjects = Subject::select('id', 'name')->get();
            $selectedSubjectIds = json_decode(Auth::user()->tutor->subjects);
            $selectedSubjects = [];
            foreach ($allSubjects as $subject) {
                if (in_array($subject['id'], $selectedSubjectIds)) {
                    array_push($selectedSubjects, $subject);
                }
            }
            return Inertia::render('Calendar', [
                'selectedSubjects' => $selectedSubjects,
            ]);
        } else if (Auth::user()->role->name === 'student') {
            return Inertia::render('Calendar');
        }
    }
}
