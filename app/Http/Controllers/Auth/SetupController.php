<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Inertia\Inertia;
use App\Models\Tutor;
use App\Models\Student;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class SetupController extends Controller
{

    public function index()
    {
        $role = Auth::user()->role->name;
        if ($role != 'admin' && !DB::table($role . 's')->where('user_id', Auth::user()->id)->exists()) {
            return Inertia::render('Auth/Setup', ['subjects' => Subject::select('id', 'name')->get()]);
        }
        return redirect('dashboard');
    }

    protected function create(Request $request)
    {
        if (Auth::user()->role->name === 'tutor') {
            $request->validate([
                'meeting_link' => 'required',
                'bio' => 'required|max:1000',
                'subject_id' => 'required'
            ]);
            $meetinglink = $request->meeting_link;
            if (strpos($meetinglink, 'http://') === false && strpos($meetinglink, 'https://') === false) {
                $meetinglink = 'https://' . $meetinglink;
            }
            Tutor::create([
                'user_id' => Auth::user()->id,
                'meeting_link' => $meetinglink,
                'bio' => $request->bio,
                'subjects' => json_encode([intval($request->subject_id)])
            ]);
        } else if (Auth::user()->role->name === 'student') {
            $request->validate([
                'terms' => 'required|accepted'
            ]);
            Student::create([
                'user_id' => Auth::user()->id,
                'terms' => true
            ]);
        }
        return redirect('dashboard');
    }
}
