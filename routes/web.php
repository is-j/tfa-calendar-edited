<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\DashboardController;
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
    Route::any('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/settings', fn () => view('settings'))->name('settings');
    Route::get('/api/slot/get/{id}', [ApiController::class, 'getSlot']);
    Route::post('/api/slot/cancel', [ApiController::class, 'cancelSlot']);
    /*
    Route::get('/reset', [ResetController::class, 'index'])->name('reset');
    Route::post('/reset', [ResetController::class, 'reset']);*/
    Route::post('/cancel', fn () => view('cancel'));
    Route::get('/api/report', [ApiController::class, 'initReport']);
    Route::post('/api/report', [ApiController::class, 'sendReport']);
    Route::get('/ml/{slotid}', [ApiController::class, 'redirectMeetingLink']);
    Route::get('/update-password', fn () => view('auth.update-password'))->name('update-password');
});


Route::middleware(['auth', 'setup', 'tutor'])->group(function () {
    Route::post('/api/slot/create', [ApiController::class, 'createSlot']);
    Route::get('/api/subject/get', [ApiController::class, 'getSubject']);
    Route::post('/api/subject/plus', [ApiController::class, 'plusSubject']);
    Route::post('/api/subject/minus', [ApiController::class, 'minusSubject']);
    Route::post('/api/information/update', [ApiController::class, 'updateInformation']);
});

Route::middleware(['auth', 'setup', 'student'])->group(function () {
    Route::post('/api/slot/claim', [ApiController::class, 'claimSlot']);
});

Route::middleware(['auth', 'setup', 'admin'])->group(function () {
    Route::get('/admin/users', [AdminController::class, 'users'])->name('admin.users');
    Route::get('/admin/reports', [AdminController::class, 'reports'])->name('admin.reports');
    Route::get('/admin/subjects', [AdminController::class, 'subjects'])->name('admin.subjects');
    Route::get('/api/admin/users', [ApiController::class, 'getUser']);
    Route::get('/api/admin/reports', [ApiController::class, 'getReport']);
    Route::get('/api/admin/subjects', [ApiController::class, 'getSubjectAll']);
    Route::post('/api/subject/create', [ApiController::class, 'createSubject']);
    Route::post('/api/report/confirm', [ApiController::class, 'confirmReport']);
    Route::post('/api/report/deny', [ApiController::class, 'denyReport']);
});
Route::middleware('auth')->group(function () {
    Route::get('/setup', [SetupController::class, 'index'])->name('setup');
    Route::post('/setup', [SetupController::class, 'create']);
});
