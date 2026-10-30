<?php

use App\Models\User;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('user:create {name} {email}', function (string $name, string $email) {
    $user = User::create([
        'name' => $name,
        'email' => $email,
        'password' => $this->secret('Password'),
    ]);

    $this->info("User {$user->email} created.");
})->purpose('Create a user that can log in to the website');
