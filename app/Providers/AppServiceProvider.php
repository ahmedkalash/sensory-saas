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
        // 1. Auto-migrate on startup for desktop environment
        // We use a cache key to only run this once per app session (or hour) to keep the UI fast.
        if (app()->environment('production') || config('app.debug') === false) {
            $currentVersion = config('app.version', '1.0.0');
            $migratedVersion = \Illuminate\Support\Facades\Cache::get('migrated_version');

            if ($migratedVersion !== $currentVersion) {
                // 0. Ensure SQLite database file exists (because we excluded it from the installer)
                $dbPath = database_path('database.sqlite');
                if (! file_exists($dbPath)) {
                    touch($dbPath);
                }

                // 1. Run migrations
                \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);

                // 2. Run DatabaseSeeder
                \Illuminate\Support\Facades\Artisan::call('db:seed', ['--force' => true]);

                \Illuminate\Support\Facades\Cache::forever('migrated_version', $currentVersion);
            }
        }
    }
}
