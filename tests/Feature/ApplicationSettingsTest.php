<?php

namespace Tests\Feature;

use App\Filament\Pages\Settings;
use App\Models\User;
use App\Services\ApplicationSettings;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ApplicationSettingsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Pastikan cache tidak berbasi pada data test sebelumnya.
        Cache::flush();
    }

    public function test_default_settings_returned_when_database_is_empty(): void
    {
        $settings = app(ApplicationSettings::class);

        // Fallback default saat database settings belum terisi
        $this->assertNull($settings->get('app.name'));
        $this->assertSame('SIMA UNMARIS', $settings->getAppName());
        $this->assertSame('Universitas Stella Maris', $settings->getInstitutionName());
        $this->assertNull($settings->get('app.logo'));
        $this->assertSame('images/logo-unmaris.png', $settings->getLogo());
    }

    public function test_can_get_and_set_application_name(): void
    {
        $settings = app(ApplicationSettings::class);

        $this->assertSame('SIMA UNMARIS', $settings->getAppName());

        $settings->set('app.name', 'SIMA UNMARIS 2.0');

        $this->assertSame('SIMA UNMARIS 2.0', $settings->getAppName());
    }

    public function test_can_get_and_replace_logo(): void
    {
        $settings = app(ApplicationSettings::class);

        $settings->set('app.logo', 'branding/new-logo.png');
        $this->assertSame('branding/new-logo.png', $settings->get('app.logo'));

        $settings->set('app.logo', 'branding/another-logo.png');
        $this->assertSame('branding/another-logo.png', $settings->get('app.logo'));
    }

    public function test_fallback_logo_when_file_is_not_available(): void
    {
        $settings = app(ApplicationSettings::class);
        $settings->set('app.logo', 'branding/missing-file.png');

        $this->assertSame(asset('images/logo-unmaris.png'), $settings->getLogoUrl());
    }

    public function test_cache_is_invalidated_after_settings_are_saved(): void
    {
        $settings = app(ApplicationSettings::class);

        // Populate cache terlebih dahulu (misalnya saat render halaman publik).
        $this->assertSame('SIMA UNMARIS', $settings->getAppName());
        $this->assertTrue(Cache::has(ApplicationSettings::CACHE_KEY));

        $settings->set('app.name', 'SIMA UNMARIS 2.0');

        // Cache harus langsung invalid (di-flush) setelah penyimpanan.
        $this->assertFalse(Cache::has(ApplicationSettings::CACHE_KEY));

        // Nilai berikutnya harus dibaca dari database, bukan cache lama.
        $this->assertSame('SIMA UNMARIS 2.0', $settings->getAppName());
        $this->assertSame('SIMA UNMARIS 2.0', $settings->get('app.name'));
    }

    public function test_public_landing_page_renders_dynamic_branding(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('SIMA UNMARIS');
    }

    public function test_public_asset_check_page_renders_dynamic_branding(): void
    {
        $response = $this->get('/cek-aset?code=UNMARIS/IT/2026/001');

        $response->assertStatus(200);
        $response->assertSee('SIMA UNMARIS');
    }

    public function test_super_admin_can_access_branding_settings_page(): void
    {
        $user = User::factory()->create();
        $user->assignRole(Role::create(['name' => 'Super Admin']));

        $this->actingAs($user);

        $this->get('/admin/settings')->assertStatus(200);
    }

    public function test_guest_cannot_access_branding_settings_page(): void
    {
        $this->get('/admin/settings')->assertRedirect('/admin/login');
    }

    public function test_branding_action_validates_uploaded_logo(): void
    {
        $user = User::factory()->create();
        $user->assignRole(Role::create(['name' => 'Super Admin']));
        $this->actingAs($user);

        $component = Livewire::test(Settings::class);

        $component->callAction('branding', [
            'app_name' => 'SIMA UNMARIS',
            'institution_name' => 'Universitas Stella Maris',
            'app_description' => 'Sistem Informasi Manajemen Aset.',
            'app_logo' => UploadedFile::fake()->create('logo.txt', 100, 'text/plain'),
        ]);

        // File dengan tipe non-Image (txt) harus diblokir oleh validasi server-side.
        $component->assertActionHalted();
    }
}
