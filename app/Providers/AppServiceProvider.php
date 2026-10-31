<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::define('admin', fn (User $user) => $user->is_admin);

        Password::defaults(fn () => Password::min(8));

        // Dutch day and month names in translatedFormat()
        Date::setLocale(config('app.locale'));
    }
}
