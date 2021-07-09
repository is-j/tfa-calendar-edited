<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Subject;
use App\Models\Language;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class SettingsController extends Controller
{
    public function index()
    {
        if (Auth::user()->role->name === 'tutor') {
            $allSubjects = Subject::select('id', 'name')->get();
            $selectedSubjectIds = json_decode(Auth::user()->tutor->subjects);
            $userSubjects = [];
            foreach ($allSubjects as $subject) {
                $userSubjects[$subject['id']] = in_array($subject['id'], $selectedSubjectIds);
            }
            $allLanguages = Language::select('id', 'name')->get();
            $selectedLanguageIds = json_decode(Auth::user()->tutor->languages);
            $userLanguages = [];
            foreach ($allLanguages as $language) {
                $userLanguages[$language['id']] = in_array($language['id'], $selectedLanguageIds);
            }
            return Inertia::render('Settings', [
                'allSubjects' => $allSubjects,
                'userSubjects' => $userSubjects,
                'allLanguages' => $allLanguages,
                'userLanguages' => $userLanguages,
            ]);
        } else if (Auth::user()->role->name === 'student') {
            return Inertia::render('Settings');
        }
    }
}
