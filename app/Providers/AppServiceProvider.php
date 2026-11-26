<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

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
        \Illuminate\Support\Facades\Gate::define('superadmin-only', function ($user) {
            return $user->role === 'superadmin';
        });

        \Illuminate\Support\Facades\Gate::define('admin-only', function ($user) {
            return in_array($user->role, ['superadmin', 'admin_bidang']);
        });

        \Illuminate\Support\Facades\Gate::define('isParticipant', function ($user) {
            return $user->role === 'participant';
        });
    }
}