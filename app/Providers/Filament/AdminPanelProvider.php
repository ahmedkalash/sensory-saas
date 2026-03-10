<?php

namespace App\Providers\Filament;

use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\HtmlString;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function boot(): void
    {
        Select::configureUsing(function (Select $select) {
            $select->native(false);
        });
    }

    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('/')
            ->brandName('SRPS Clinical')
            ->font('Outfit')
            ->topNavigation()
            ->sidebarCollapsibleOnDesktop()
            ->colors([
                'primary' => Color::Cyan,
            ])
            ->darkMode(false)
            ->renderHook(
                'panels::styles.after',
                fn() => new HtmlString('<link rel="stylesheet" href="/css/custom-filament.css">'),
            )
            ->renderHook(
                'panels::body.start',
                fn() => new HtmlString(<<<'HTML'
<div id="srps-loader" style="
    position: fixed; inset: 0; z-index: 99999;
    background: linear-gradient(135deg, #f0f9ff 0%, #ede9fe 50%, #f0fdfa 100%);
    display: flex; flex-direction: column;
    align-items: center; justify-content: center;
    gap: 20px; transition: opacity 0.4s ease;
">
    <svg width="64" height="64" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
        <circle cx="32" cy="32" r="28" stroke="#0891b2" stroke-width="4" stroke-opacity="0.2"/>
        <path d="M32 4 A28 28 0 0 1 60 32" stroke="#0891b2" stroke-width="4" stroke-linecap="round">
            <animateTransform attributeName="transform" type="rotate" from="0 32 32" to="360 32 32" dur="0.9s" repeatCount="indefinite"/>
        </path>
        <circle cx="32" cy="32" r="14" fill="#0891b2" fill-opacity="0.08"/>
        <path d="M24 28 C24 24 28 20 32 20 C36 20 40 24 40 28 C40 32 36 34 32 36 C28 34 24 32 24 28 Z" fill="#0891b2" fill-opacity="0.3"/>
        <path d="M28 36 C28 38 30 42 32 42 C34 42 36 38 36 36" stroke="#0891b2" stroke-width="2" fill="none" stroke-linecap="round"/>
    </svg>
    <div style="text-align:center; font-family: 'Outfit', sans-serif;">
        <div style="font-size: 1.4rem; font-weight: 700; color: #0e4a5c; letter-spacing: -0.02em;">SRPS Clinical</div>
        <div style="font-size: 0.85rem; color: #64748b; margin-top: 4px; font-family: 'Noto Kufi Arabic', sans-serif;">جارٍ تحميل التطبيق...</div>
    </div>
</div>
<script>
    window.addEventListener('load', function () {
        const loader = document.getElementById('srps-loader');
        if (loader) {
            loader.style.opacity = '0';
            setTimeout(function () { loader.remove(); }, 450);
        }
    });
</script>
HTML),
            )
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->userMenuItems([
                'profile' => Action::make('profile')->visible(false),
                'logout' => Action::make('logout')->visible(false),
            ])
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
                \App\Http\Middleware\AutoLoginMiddleware::class,
            ]);
    }
}
