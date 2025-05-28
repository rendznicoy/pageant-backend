<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL; // Add this import

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
        // Force HTTPS in production
        if (app()->environment('production')) {
            URL::forceScheme('https');
        }

        if (app()->runningInConsole() && config('database.default') !== null) {
            try {
                DB::table('password_reset_tokens')
                    ->where('created_at', '<', now()->subDay())
                    ->delete();
            } catch (\Exception $e) {
                // Avoid crashing during build/deploy
                logger()->warning('Skipping DB cleanup during package:discover: ' . $e->getMessage());
            }
        }
    }
}