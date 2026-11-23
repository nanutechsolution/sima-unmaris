<?php

namespace App\Filament\Pages;

use App\Enums\NavigationGroupEnum;
use App\Services\ApplicationSettings;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Storage;

class Settings extends Page
{
    use HasPageShield;

    protected static ?string $modelLabel = 'Pengaturan';

    protected static ?string $title = 'Pengaturan Branding';

    protected static ?string $navigationLabel = 'Pengaturan Branding';

    protected static ?string $slug = 'settings';

    protected static string|\UnitEnum|null $navigationGroup = NavigationGroupEnum::MASTER_DATA->value;

    protected static ?int $navigationSort = 7;

    protected string $view = 'filament.pages.settings';

    /**
     * Hanya Super Admin yang boleh mengakses halaman pengaturan branding.
     */
    public static function canAccess(): bool
    {
        $user = auth()->user();

        if (! $user) {
            return false;
        }

        return $user->hasRole('Super Admin');
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('branding')
                ->label('Pengaturan Branding')
                ->icon('heroicon-o-cog')
                ->color('primary')
                ->modalHeading('Pengaturan Branding')
                ->modalDescription('Perbarui identitas aplikasi, institusi, deskripsi, serta logo publik.')
                ->modalSubmitActionLabel('Simpan Perubahan')
                ->form([
                    TextInput::make('app_name')
                        ->label('Nama Aplikasi')
                        ->default(app(ApplicationSettings::class)->getAppName())
                        ->required(),
                    TextInput::make('institution_name')
                        ->label('Nama Institusi')
                        ->default(app(ApplicationSettings::class)->getInstitutionName())
                        ->required(),
                    Textarea::make('app_description')
                        ->label('Deskripsi Aplikasi')
                        ->default(app(ApplicationSettings::class)->getAppDescription()),
                    FileUpload::make('app_logo')
                        ->label('Logo Aplikasi')
                        ->disk('public')
                        ->directory('branding')
                        ->visibility('public')
                        ->maxSize(2048)
                        ->rules(['mimes:png,jpg,jpeg', 'max:2048']),
                    FileUpload::make('app_logo_dark')
                        ->label('Logo Alternatif (Mode Gelap)')
                        ->disk('public')
                        ->directory('branding')
                        ->visibility('public')
                        ->maxSize(2048)
                        ->rules(['mimes:png,jpg,jpeg', 'max:2048']),
                    FileUpload::make('app_favicon')
                        ->label('Favicon')
                        ->disk('public')
                        ->directory('branding')
                        ->visibility('public')
                        ->maxSize(2048)
                        ->rules(['mimes:png,jpg,jpeg', 'max:2048']),
                ])
                ->action(function (array $data) {
                    $settings = app(ApplicationSettings::class);

                    $settings->set('app.name', $data['app_name']);
                    $settings->set('institution.name', $data['institution_name']);
                    $settings->set('app.description', $data['app_description'] ?? '');
                    $settings->set('app.favicon', $data['app_favicon'] ?? 'images/logo-unmaris.png');

                    if (! empty($data['app_logo'])) {
                        $oldLogo = $settings->get('app.logo');
                        $settings->set('app.logo', $data['app_logo']);

                        // Hapus file lama setelah penggantian berhasil disimpan
                        if ($oldLogo && $oldLogo !== $data['app_logo']) {
                            Storage::disk('public')->delete($oldLogo);
                        }
                    }

                    if (! empty($data['app_logo_dark'])) {
                        $settings->set('app.logo_dark', $data['app_logo_dark']);
                    }

                    Notification::make()
                        ->title('Branding Berhasil Disimpan')
                        ->body('Perubahan pengaturan branding telah diterapkan secara otomatis pada halaman publik.')
                        ->success()
                        ->send();
                }),
        ];
    }
}
