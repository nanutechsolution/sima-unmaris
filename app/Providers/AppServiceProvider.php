<?php

namespace App\Providers;

use App\Policies\PermissionPolicy;
use App\Policies\RolePolicy;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\ServiceProvider;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::policy(Role::class, RolePolicy::class);
        Gate::policy(Permission::class, PermissionPolicy::class);

        // 2. (Opsional tapi Sangat Disarankan) Hak Istimewa Mutlak untuk Super Admin
        // Ini memastikan Super Admin selalu bisa melewati semua halangan (kecuali yang kita hardcode false di Policy)
        Gate::before(function ($user, $ability) {
            return $user->hasRole('Super Admin') ? true : null;
        });

        // 3. Reset password via email -> invalidate semua session aktif (logout dari semua device)
        Event::listen(\Illuminate\Auth\Events\PasswordReset::class, function ($event) {
            try {
                DB::table('sessions')->where('user_id', $event->user->id)->delete();
            } catch (\Throwable $e) {
                // Tabel sessions mungkin belum ada / driver berbeda; tidak fatal.
            }
        });
    }
}
