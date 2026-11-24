<?php

namespace App\Providers\Filament;

use App\Enums\NavigationGroupEnum;
use App\Filament\Pages\Auth\EditProfile as AuthEditProfile;
use App\Services\ApplicationSettings;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
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
use Illuminate\Contracts\Support\Htmlable;
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
            ->passwordReset()
            ->profile(AuthEditProfile::class)
            ->colors([
                // Primary memakai navy logo (#1B2A66) agar konsisten dengan halaman publik
                // dan kontras teks putih pada tombol tetap aman dibaca.
                'primary' => Color::hex('#1B2A66'),
                'warning' => Color::Amber,
                'success' => Color::Emerald,
                'danger' => Color::Rose,
                'info' => Color::Sky,
                'gray' => Color::Slate,
            ])
            ->brandName(fn (): string => app(ApplicationSettings::class)->getAppName())
            ->brandLogo(fn (): Htmlable => view('filament.partials.brand-logo', ['dark' => false]))
            ->darkModeBrandLogo(fn (): Htmlable => view('filament.partials.brand-logo', ['dark' => true]))
            ->brandLogoHeight('2.25rem')
            ->favicon(fn (): string => app(ApplicationSettings::class)->getFaviconUrl())
            ->databaseNotifications()
            ->sidebarCollapsibleOnDesktop()
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
                    ->label(NavigationGroupEnum::SERVICE_SATISFACTION->value)
                    ->icon(Heroicon::OutlinedChatBubbleBottomCenterText)
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
            ->plugins([
                FilamentShieldPlugin::make()->navigationGroup(NavigationGroupEnum::SYSTEM_AUDIT->value),

            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
