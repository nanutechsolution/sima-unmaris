<?php

namespace App\Providers\Filament;

use App\Enums\NavigationGroupEnum;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationGroup;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->colors([
                'primary' => Color::hex('#FDE01A'), // Biru Dongker Logo
                'warning' => Color::hex('#1B1464'), // Kuning Emas Logo
                'success' => Color::Emerald,
                'danger'  => Color::Rose,
                'info'    => Color::Sky,
                'gray'    => Color::Slate,
            ])
            // ->brandLogo(asset('images/logo-unmaris.png'))
            ->brandLogoHeight('2.5rem')
            ->brandName('SIMA Stella Maris')
            ->favicon(asset('images/logo-unmaris.png'))
            ->databaseNotifications()
            ->navigationGroups([
                NavigationGroup::make()
                    ->label(NavigationGroupEnum::MASTER_DATA->value)
                    ->icon(Heroicon::OutlinedNewspaper)
                    ->collapsed(),
                NavigationGroup::make()
                    ->label(NavigationGroupEnum::ASSET_MANAGEMENT->value)
                    ->icon(Heroicon::OutlinedCube)
                    ->collapsed(),
                NavigationGroup::make()
                    ->label(NavigationGroupEnum::INVENTORY_ATK->value)
                    ->icon(Heroicon::OutlinedArchiveBox)
                    ->collapsed(),
                NavigationGroup::make()
                    ->label(NavigationGroupEnum::SYSTEM_AUDIT->value)
                    ->icon(Heroicon::OutlinedShieldCheck)
                    ->collapsed(),
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                // AccountWidget::class,
                // FilamentInfoWidget::class,
            ])
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
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
