<?php

namespace App\Http\Controllers;

use App\Models\Tutor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LanguageController extends Controller
{
    public function update(Request $request)
    {
        $selectedLanguages = [];
        foreach ($request->all() as $key => $value) {
            if ($value === true) {
                array_push($selectedLanguages, $key);
            }
        }
        if (count($selectedLanguages) === 0) {
            return back()->withErrors(['languages' => 'At least one language must be selected.']);
        } else {
            Tutor::where('user_id', Auth::user()->id)->update(['languages' => json_encode($selectedLanguages)]);
            return back();
        }
    }
}
