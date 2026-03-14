<?php

namespace App\Providers;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // 0. Ensure SQLite database file exists at the earliest possible moment
        // This prevents crashes if other providers or early middleware hit the DB
        $dbPath = database_path('database.sqlite');
        if (! file_exists($dbPath)) {
            $dbDir = dirname($dbPath);
            if (! is_dir($dbDir)) {
                mkdir($dbDir, 0755, true);
            }
            touch($dbPath);
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // 1. Auto-migrate on startup for desktop environment
        if (! app()->runningInConsole() && (app()->environment('production') || config('app.debug') === false)) {
            $currentVersion = config('app.version', '1.0.0');
            $migratedVersion = Cache::get('migrated_version');

            // Hardened check: even if cache matches, verify the users table exists.
            // This prevents "No such table" errors if the cache is out of sync with a fresh DB.
            $needsMigration = ($migratedVersion !== $currentVersion);

            if (!$needsMigration) {
                try {
                    if (! \Illuminate\Support\Facades\Schema::hasTable('users')) {
                        $needsMigration = true;
                    }
                } catch (\Exception $e) {
                    $needsMigration = true;
                }
            }

            if ($needsMigration) {
                // 1. Run migrations
                Artisan::call('migrate', ['--force' => true]);

                // 2. Run DatabaseSeeder
                Artisan::call('db:seed', ['--force' => true]);

                Cache::forever('migrated_version', $currentVersion);
            }
        }
    }
}
