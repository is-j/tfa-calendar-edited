<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use App\Rules\CorrectPassword;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ResetController extends Controller
{

    protected function index()
    {
        return view('auth.reset');
    }
    protected function reset(Request $request)
    {
        $request->validate([
            'password' => ['required', new CorrectPassword],
            'new_password' => ['required', 'string', 'min:8']
        ]);
        User::find(Auth::user()->id)->update(['password' => Hash::make($request->new_password)]);
        return redirect()->route('settings');
    }
}
