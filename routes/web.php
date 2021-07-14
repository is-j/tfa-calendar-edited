<?php

use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\APIController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\CancelController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\Auth\SetupController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MeetingLinkController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    if (Auth::check()) {
        return redirect('dashboard');
    } else {
        return Inertia::render('Welcome');
    }
});

Route::middleware('auth')->group(function () {
    Route::get('/setup', [SetupController::class, 'index']);
    Route::post('/setup', [SetupController::class, 'create']);
});

Route::middleware('auth', 'setup')->group(function () {
    // Pages
    Route::get('/dashboard', [DashboardController::class, 'index']);
    Route::get('/calendar', [CalendarController::class, 'index']);
    Route::get('/settings', [SettingsController::class, 'index']);
    Route::get('/cancel', [CancelController::class, 'index']);
    Route::post('/cancel', [CancelController::class, 'show']);
    Route::inertia('/update-password', 'Auth/UpdatePassword');

    // Resources
    Route::get('/ml/{id}', [MeetingLinkController::class, 'index']);
    Route::get('/events', [EventController::class, 'index']);
    Route::get('/events/{id}', [EventController::class, 'show']);
    Route::post('/events/{id}', [EventController::class, 'destroy']);
});

Route::middleware(['auth', 'setup', 'tutor'])->group(function () {
    Route::post('/events', [EventController::class, 'store']);
    Route::put('/user/subjects', [SubjectController::class, 'update']);
    Route::put('/user/languages', [LanguageController::class, 'update']);
});

Route::middleware('auth', 'setup', 'student')->group(function () {
    Route::put('/events/{id}', [EventController::class, 'update']);
});
