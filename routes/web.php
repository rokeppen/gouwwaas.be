<?php

use App\Http\Controllers\ActivityController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RegistrationController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::view('/', 'home', ['districts' => config('gouw.districts')])->name('home');
Route::view('/districten', 'districten', ['districts' => config('gouw.districts')])->name('districten');
Route::view('/wie-is-wie', 'wie-is-wie', ['districts' => config('gouw.districts')])->name('wie-is-wie');
Route::get('/activiteiten', [ActivityController::class, 'index'])->name('activiteiten.index');

Route::middleware('auth')->group(function () {
    // Before /activiteiten/{activity}, so "nieuw" isn't read as an activity slug.
    Route::get('/activiteiten/nieuw', [ActivityController::class, 'create'])->name('activiteiten.create');
    Route::post('/activiteiten', [ActivityController::class, 'store'])->name('activiteiten.store');
    Route::get('/activiteiten/{activity}/bewerken', [ActivityController::class, 'edit'])->name('activiteiten.edit');
    Route::put('/activiteiten/{activity}', [ActivityController::class, 'update'])->name('activiteiten.update');
    Route::delete('/activiteiten/{activity}', [ActivityController::class, 'destroy'])->name('activiteiten.destroy');

    Route::get('/activiteiten/{activity}/inschrijvingen', [RegistrationController::class, 'index'])->name('activiteiten.inschrijvingen');
    Route::get('/activiteiten/{activity}/inschrijvingen.csv', [RegistrationController::class, 'export'])->name('activiteiten.inschrijvingen.export');
    Route::delete('/activiteiten/{activity}/inschrijvingen/{registration}', [RegistrationController::class, 'destroy'])
        ->scopeBindings()
        ->name('activiteiten.inschrijvingen.destroy');
});

Route::get('/activiteiten/{activity}', [ActivityController::class, 'show'])->name('activiteiten.show');
Route::post('/activiteiten/{activity}/inschrijven', [RegistrationController::class, 'store'])
    ->middleware('throttle:10,1')
    ->name('activiteiten.inschrijven');

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
