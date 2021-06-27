<?php

use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\APIController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\Auth\SetupController;

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

Route::get('/', fn () => redirect('dashboard'));
Route::middleware('auth', 'setup')->group(function () {
    Route::inertia('/dashboard', 'Dashboard');
    Route::inertia('/calendar', 'Calendar');
    Route::inertia('/tutors', 'Tutors');
    Route::inertia('/settings', 'Settings');
    Route::get('/api/slot/get/{id}', [APIController::class, 'getSlot']);
    Route::get('/cancel', fn () => Inertia::render('Cancel', ['slot_id' => session()->get('slot_id')]));
    Route::post('/access/slot/cancel', [CalendarController::class, 'cancelSlot']);
});
Route::middleware('auth')->group(function () {
    Route::get('/setup', [SetupController::class, 'index']);
    Route::post('/setup', [SetupController::class, 'create']);
});
Route::middleware(['auth', 'setup', 'tutor'])->group(function () {
    Route::post('/api/slot/create', [APIController::class, 'createSlot']);
    Route::get('/api/subject/get', [APIController::class, 'getSubject']);
    Route::post('/api/subject/plus', [APIController::class, 'plusSubject']);
    Route::post('/api/subject/minus', [APIController::class, 'minusSubject']);
    Route::post('/api/information/update', [APIController::class, 'updateInformation']);
});
