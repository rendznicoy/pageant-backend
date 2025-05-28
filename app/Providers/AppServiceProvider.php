<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL; // Add this import
use App\Services\CloudinaryService; // Adjust the namespace as needed

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
        if (!env('FRONTEND_URL')) {
            throw new \Exception('FRONTEND_URL environment variable is required');
        }
        
        if (env('APP_ENV') === 'production') {
            URL::forceScheme('https');
        }

        if (app()->runningInConsole() && config('database.default') !== null) {
            try {
                DB::table('password_reset_tokens')
                    ->where('created_at', '<', now()->subDay())
                    ->delete();
            } catch (\Exception $e) {
                logger()->warning('Skipping DB cleanup during package:discover: ' . $e->getMessage());
            }
        }

        $this->app->singleton(CloudinaryService::class, function ($app) {
            return new CloudinaryService();
        });
    }
}