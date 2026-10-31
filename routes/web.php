<?php

use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::view('/', 'home', ['districts' => config('gouw.districts')])->name('home');
Route::view('/districten', 'districten', ['districts' => config('gouw.districts')])->name('districten');
Route::view('/wie-is-wie', 'wie-is-wie', ['districts' => config('gouw.districts')])->name('wie-is-wie');
Route::view('/activiteiten', 'activiteiten')->name('activiteiten');

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->middleware('throttle:5,1');

    Route::get('/wachtwoord-vergeten', [PasswordResetController::class, 'create'])->name('password.request');
    Route::post('/wachtwoord-vergeten', [PasswordResetController::class, 'store'])->middleware('throttle:5,1')->name('password.email');
    Route::get('/wachtwoord-herstellen/{token}', [PasswordResetController::class, 'edit'])->name('password.reset');
    Route::post('/wachtwoord-herstellen', [PasswordResetController::class, 'update'])->middleware('throttle:5,1')->name('password.update');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');

    Route::get('/profiel', [ProfileController::class, 'show'])->name('profiel');
    Route::put('/profiel/wachtwoord', [ProfileController::class, 'updatePassword'])->name('profiel.wachtwoord');

    Route::middleware('can:admin')->group(function () {
        Route::resource('gebruikers', UserController::class)
            ->except('show')
            ->parameters(['gebruikers' => 'gebruiker']);
        Route::post('/gebruikers/{gebruiker}/wachtwoord-reset', [UserController::class, 'forcePasswordReset'])
            ->name('gebruikers.wachtwoord-reset');
    });
});
