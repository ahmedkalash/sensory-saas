<?php

namespace App\Providers;

use Illuminate\Support\Facades\Artisan;
use Native\Desktop\Contracts\ProvidesPhpIni;
use Native\Desktop\Facades\Menu;
use Native\Desktop\Facades\Window;

class NativeAppServiceProvider implements ProvidesPhpIni
{
    /**
     * Executed once the native application has been booted.
     * Use this method to open windows, register global shortcuts, etc.
     */
    public function boot(): void
    {
        Menu::create(
            Menu::make(
                Menu::link(url('/'), 'الرئيسية'),
                Menu::separator(),
                Menu::quit('إنهاء التطبيق'),
            )->label('ملف'),

            Menu::edit('تحرير'),
            Menu::view('عرض'),
            Menu::window('نافذة'),
        );

        // Only run migrations + seeders when app version changes.
        // On normal boots this is a single cache read — near instant.
        if (cache()->get('booted_version') !== config('nativephp.version')) {
            Artisan::call('migrate', ['--force' => true]);
            Artisan::call('db:seed', ['--force' => true]);
            cache()->forever('booted_version', config('nativephp.version'));
        }

        Window::open()
            ->width(1200)
            ->height(800)
            ->minWidth(1000)
            ->minHeight(700)
            ->title('Sensory Processing Assessment Tool')
            ->backgroundColor('#f0f9ff')
            ->rememberState()
            ->hideDevTools();
    }

    /**
     * Return an array of php.ini directives to be set.
     */
    public function phpIni(): array
    {
        return [
            'opcache.enable' => 1,
            'opcache.memory_consumption' => 128,
            'opcache.interned_strings_buffer' => 16,
            'opcache.max_accelerated_files' => 10000,
            'opcache.validate_timestamps' => 0,
            'realpath_cache_size' => '4096K',
            'realpath_cache_ttl' => 600,
            'memory_limit' => '1G',
            'max_execution_time' => '500',
            
            // Handle large assessment forms with many inputs
            'max_input_vars' => 5000,
            
            // Increase post/upload limits in case of database imports or file uploads
            'post_max_size' => '128M',
            'upload_max_filesize' => '128M',
            
            // Clean production behavior (Laravel still handles/logs exceptions properly)
            'display_errors' => 0,
        ];
    }
}
