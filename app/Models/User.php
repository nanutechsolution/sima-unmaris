<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasAvatar;
use Filament\Panel;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements FilamentUser, HasAvatar
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, HasRoles, HasUuids, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'username_siakad', // Field tambahan untuk sinkronisasi
        'role_name',       // Field tambahan untuk sinkronisasi
        'avatar_url',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // --- FILAMENT AUTHORIZATION ---
    public function canAccessPanel(Panel $panel): bool
    {
        $user = auth()->user();

        // Tamu yang belum terautentikasi tidak boleh mengakses panel
        if (! $user) {
            return false;
        }

        // Hak akses diberikan berdasarkan peran (Super Admin, Staf Inventaris, & Auditor)
        // Super Admin memiliki akses penuh tanpa batasan akses.
        if ($user->hasRole('Super Admin')) {
            return true;
        }

        // Staf Inventaris mengelola aset & inventaris, Auditor hanya membaca audit trail
        return $user->hasRole(['Staf Inventaris', 'Auditor']);
    }

    public function getFilamentAvatarUrl(): ?string
    {

        return $this->avatar_url ? Storage::url($this->avatar_url) : null;
    }
}
