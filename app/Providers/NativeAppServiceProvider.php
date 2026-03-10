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

        Window::open()
            ->width(1200)
            ->height(800)
            ->minWidth(1000)
            ->minHeight(700)
            ->title('Sensory Processing Assessment Tool')
            ->backgroundColor('#f0f9ff')
            ->rememberState();

        // Run pending migrations only once per app version.
        $migratedVersion = cache()->get('migrated_version');

        if ($migratedVersion !== config('nativephp.version')) {
            Artisan::call('migrate', ['--force' => true]);
            cache()->forever('migrated_version', config('nativephp.version'));
        }

        // Run seeders — DatabaseSeeder tracks each one individually
        // so only unseeded classes run. Safe to call on every boot.
        Artisan::call('db:seed', ['--force' => true]);
    }

    /**
     * Return an array of php.ini directives to be set.
     */
    public function phpIni(): array
    {
        return [
            'opcache.enable' => 1,
            'opcache.memory_consumption' => 128,
        ];
    }
}
