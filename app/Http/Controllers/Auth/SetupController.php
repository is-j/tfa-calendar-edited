<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Models\Tutor;
use App\Models\Student;
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
            return view('auth.setup');
        } else {
            return redirect('dashboard');
        }
    }

    protected function create(Request $request)
    {
        if (Auth::user()->role->name == 'tutor') {
            $request->validate([
                'meeting_link' => 'required',
                'bio' => 'required|max:1000',
                'subject' => 'required'
            ]);
            $meetinglink = $request->meeting_link;
            if (!(strpos($meetinglink, 'http://') !== false || strpos($meetinglink, 'https://') !== false)) {
                $meetinglink = 'https://' . $meetinglink;
            }
            Tutor::create([
                'user_id' => Auth::user()->id,
                'meeting_link' => $meetinglink,
                'bio' => $request->bio,
                'subjects' => json_encode([intval($request->subject)])
            ]);
        } else if (Auth::user()->role->name == 'student') {
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
