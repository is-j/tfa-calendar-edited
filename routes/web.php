<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AjaxController;
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

Auth::routes();

Route::middleware(['auth', 'setup'])->group(function () {
    Route::get('/', [DashboardController::class, 'index']);
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/settings', function () {
        return view('settings');
    })->name('settings');
    Route::get('/ajax/get', [AjaxController::class, 'get']);
    Route::post('/ajax/create', [AjaxController::class, 'create']);
    Route::post('/ajax/cancel', [AjaxController::class, 'cancel']);
});

Route::middleware('auth')->group(function () {
    Route::get('/setup', [SetupController::class, 'index'])->name('setup');
    Route::post('/setup', [SetupController::class, 'create']);
});