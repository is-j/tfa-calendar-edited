<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AjaxController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Auth\ResetController;
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

Route::middleware(['auth', 'setup'])->group(function () {
    Route::get('/', [DashboardController::class, 'redirect']);
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/settings', function () {
        return view('settings');
    })->name('settings');
    Route::get('/ajax/get/{id}', [AjaxController::class, 'get']);
    Route::post('/ajax/cancel', [AjaxController::class, 'cancel']);
    Route::get('/reset', [ResetController::class, 'index'])->name('reset');
    Route::post('/reset', [ResetController::class, 'reset']);
    Route::post('/cancel', function() {
        return view('cancel');
    })->name('cancel');
});

Route::middleware(['auth', 'setup', 'tutor'])->group(function () {
    Route::post('/ajax/create', [AjaxController::class, 'create']);
    Route::post('/ajax/subject/plus', [AjaxController::class, 'plusSubject']);
    Route::post('/ajax/subject/minus', [AjaxController::class, 'minusSubject']);
    Route::post('/ajax/information/update', [AjaxController::class, 'updateInformation']);
});

Route::middleware(['auth', 'setup', 'student'])->group(function () {
    Route::post('/ajax/claim', [AjaxController::class, 'claim']);
});

Route::middleware(['auth', 'setup', 'admin'])->group(function () {
    Route::get('/dashboard/users', [AdminController::class, 'claim'])->name('admin.users');
});

Route::middleware('auth')->group(function () {
    Route::get('/setup', [SetupController::class, 'index'])->name('setup');
    Route::post('/setup', [SetupController::class, 'create']);
    Route::get('/ml/{eventid}', [AjaxController::class, 'meetingLink'])->name('meetinglink');
});