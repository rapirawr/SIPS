<?php

namespace App\Providers;

use App\Models\Pengaduan;
use App\Models\User;
use App\Policies\PengaduanPolicy;
use Illuminate\Support\Facades\Gate;
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
        // Force HTTPS if running behind Ngrok or another proxy
        if (request()->header('x-forwarded-proto') === 'https') {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }

        // Register model policies
        Gate::policy(Pengaduan::class, PengaduanPolicy::class);

        // Gate untuk manage-users (admin only)
        Gate::define('manage-users', function (User $user) {
            return $user->isAdmin();
        });

        // Gate untuk manage-kategori (admin only)
        Gate::define('manage-kategori', function (User $user) {
            return $user->isAdmin();
        });
    }
}
