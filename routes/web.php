<?php

use App\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::view('/', 'home', ['districts' => config('gouw.districts')])->name('home');
Route::view('/wie-is-wie', 'wie-is-wie', ['districts' => config('gouw.districts')])->name('wie-is-wie');
Route::view('/activiteiten', 'activiteiten')->name('activiteiten');

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->middleware('throttle:5,1');
});

Route::post('/logout', [LoginController::class, 'destroy'])->middleware('auth')->name('logout');
