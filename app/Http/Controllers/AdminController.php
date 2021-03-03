<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminController extends Controller
{
    protected function users()
    {
        return view('admin.users');
    }
    protected function probations()
    {
        return view('admin.probations');
    }
    protected function reports()
    {
        return view('admin.reports');
    }
    protected function subjects()
    {
        return view('admin.subjects');
    }
}
