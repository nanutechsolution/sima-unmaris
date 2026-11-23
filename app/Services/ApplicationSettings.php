<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

class ApplicationSettings
{
    /**
     * Kunci cache untuk seluruh data settings aplikasi.
     */
    public const CACHE_KEY = 'app.branding.settings';

    /**
     * Path fallback logo publik jika belum ada pengaturan/logo yang valid.
     */
    public const DEFAULT_LOGO = 'images/logo-unmaris.png';

    /**
     * Mengambil nilai settings berdasarkan key.
     * Mengembalikan nilai default jika key belum tersedia/di-nilai kosong.
     */
    public function get(string $key, mixed $default = null): mixed
    {
        $settings = $this->all();

        $value = $settings[$key] ?? null;

        if ($value === null || $value === '') {
            return $default;
        }

        return $value;
    }

    /**
     * Mengambil seluruh isi settings (sudah di-cache).
     */
    public function all(): array
    {
        return Cache::remember(self::CACHE_KEY, now()->addHours(24), function (): array {
            return Setting::query()->pluck('value', 'key')->all();
        });
    }

    /**
     * Menyimpan/menyegera nilai settings dan langsung memaksa invalidasi cache.
     */
    public function set(string $key, mixed $value): void
    {
        Setting::query()->updateOrCreate(
            ['key' => $key],
            ['value' => (string) ($value ?? '')],
        );

        Cache::forget(self::CACHE_KEY);
    }

    // ==============================
    // BRANDING HELPERS
    // ==============================

    public function getAppName(): string
    {
        return (string) $this->get('app.name', 'SIMA UNMARIS');
    }

    public function getInstitutionName(): string
    {
        return (string) $this->get('institution.name', 'Universitas Stella Maris');
    }

    public function getAppDescription(): string
    {
        return (string) $this->get('app.description', 'Sistem Informasi Manajemen Aset Universitas Stella Maris');
    }

    public function getLogo(): ?string
    {
        return $this->get('app.logo', self::DEFAULT_LOGO);
    }

    public function getLogoDark(): ?string
    {
        $logoDark = $this->get('app.logo_dark');

        // Fallback ke logo utama jika logo mode gelap belum diunggah.
        return $logoDark ?: $this->getLogo();
    }

    public function getFavicon(): ?string
    {
        return $this->get('app.favicon', self::DEFAULT_LOGO);
    }

    /**
     * Membangun URL logo yang valid. Mengembalikan fallback default jika belum ada/valid.
     */
    public function getLogoUrl(): string
    {
        $logo = $this->getLogo();

        if ($logo) {
            $path = ltrim((string) $logo, '/');

            if ($path !== '' && file_exists(public_path($path))) {
                return asset($path);
            }

            if ($path !== '' && file_exists(storage_path('app/public/'.$path))) {
                return asset('storage/'.$path);
            }
        }

        return asset('images/logo-unmaris.png');
    }

    /**
     * Membangun URL favicon yang valid. Mengembalikan fallback default jika belum ada/valid.
     */
    public function getFaviconUrl(): string
    {
        $favicon = $this->getFavicon();

        if ($favicon) {
            $path = ltrim((string) $favicon, '/');

            if ($path !== '' && file_exists(public_path($path))) {
                return asset($path);
            }

            if ($path !== '' && file_exists(storage_path('app/public/'.$path))) {
                return asset('storage/'.$path);
            }
        }

        return asset('images/logo-unmaris.png');
    }
}
