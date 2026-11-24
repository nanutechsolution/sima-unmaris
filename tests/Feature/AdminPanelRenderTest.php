<?php

namespace Tests\Feature;

use App\Models\Asset;
use App\Models\Category;
use App\Models\FacilityFeedback;
use App\Models\Location;
use App\Models\Room;
use App\Models\User;
use Filament\Auth\Pages\Login;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Activitylog\Models\Activity;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AdminPanelRenderTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_with_super_admin_role_can_log_in_to_panel(): void
    {
        $user = User::factory()->create(['password' => 'password123']);
        $user->assignRole(Role::create(['name' => 'super_admin']));

        Filament::setCurrentPanel(Filament::getPanel('admin'));

        // Meniru alur login nyata di form admin. Role harus sama persis dengan
        // yang tersimpan di database (super_admin), bukan label "Super Admin".
        Livewire::test(Login::class)
            ->set('data.email', $user->email)
            ->set('data.password', 'password123')
            ->call('authenticate')
            ->assertHasNoErrors();

        $this->assertAuthenticatedAs($user);
        $this->get('/admin')->assertOk();
    }

    public function test_user_without_admin_role_cannot_access_panel(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $this->get('/admin')->assertForbidden();
    }

    public function test_admin_pages_render(): void
    {
        $user = User::factory()->create();
        $user->assignRole(Role::create(['name' => 'super_admin']));
        $this->actingAs($user);

        Activity::create([
            'log_name' => 'default',
            'description' => 'Data aset diubah',
            'event' => 'updated',
            'subject_type' => User::class,
            'subject_id' => $user->id,
            'properties' => ['old' => ['name' => 'Lama'], 'attributes' => ['name' => 'Baru']],
        ]);

        $category = Category::create(['name' => 'Komputer', 'prefix_code' => 'KMP']);
        $location = Location::create(['name' => 'Gedung Rektorat']);
        $room = Room::create(['location_id' => $location->id, 'name' => 'Lab Komputer', 'code' => 'LAB-01']);
        $asset = Asset::create([
            'asset_code' => 'UNMARIS/IT/2026/001',
            'name' => 'Laptop Lenovo ThinkPad',
            'category_id' => $category->id,
            'room_id' => $room->id,
            'pic_user_id' => $user->id,
            'acquisition_value' => 15000000,
            'acquisition_date' => now()->toDateString(),
            'status' => 'available',
            'condition' => 'good',
            'qr_signature_hash' => 'sig-abc-123',
        ]);

        $survey = FacilityFeedback::create([
            'title' => 'Survei Layanan Sarpras',
            'description' => 'Bantu kami meningkatkan layanan.',
            'status' => 'active',
            'form_schema' => [
                ['type' => 'rating', 'data' => ['question' => 'Kualitas layanan', 'is_required' => true]],
            ],
        ]);

        $routes = [
            '/admin',
            '/admin/assets',
            '/admin/assets/create',
            '/admin/assets/'.$asset->id.'/edit',
            '/admin/categories',
            '/admin/categories/'.$category->id.'/edit',
            '/admin/locations',
            '/admin/locations/'.$location->id.'/edit',
            '/admin/suppliers',
            '/admin/departments',
            '/admin/asset-loans',
            '/admin/maintenance-logs',
            '/admin/stock-opnames',
            '/admin/inventory-items',
            '/admin/inventory-transactions',
            '/admin/facility-feedback',
            '/admin/facility-feedback/create',
            '/admin/facility-feedback/'.$survey->id.'/edit',
            '/admin/feedback-categories',
            '/admin/users',
            '/admin/users/'.$user->id.'/edit',
            '/admin/settings',
            '/admin/report-generator',
            '/admin/system-audit-logs',
            '/admin/profile',
        ];

        foreach ($routes as $route) {
            $this->get($route)->assertOk();
        }

        $this->get('/admin')
            ->assertSee('SIMA UNMARIS')
            ->assertSee('logo-unmaris.png');
    }
}
