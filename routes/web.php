<?php

use Illuminate\Support\Facades\Route;
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
