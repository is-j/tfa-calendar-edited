<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiController;
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

Route::middleware('auth')->group(function() {
    Route::get('/setup', [SetupController::class, 'index'])->name('setup');
    Route::post('/setup', [SetupController::class, 'create']);
});

Route::middleware('auth', 'setup')->group(function() {
    Route::inertia('/dashboard', 'Dashboard')->name('dashboard');
    Route::inertia('/calendar', 'Calendar')->name('calendar');
});

Route::middleware(['auth', 'setup', 'tutor'])->group(function () {
    Route::post('/api/slot/create', [ApiController::class, 'createSlot']);
    Route::get('/api/subject/get', [ApiController::class, 'getSubject']);
    Route::post('/api/subject/plus', [ApiController::class, 'plusSubject']);
    Route::post('/api/subject/minus', [ApiController::class, 'minusSubject']);
    Route::post('/api/information/update', [ApiController::class, 'updateInformation']);
});