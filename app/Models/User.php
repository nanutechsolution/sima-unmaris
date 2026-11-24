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
        // Hak akses diberikan berdasarkan peran (super_admin, staf_inventaris, & auditor).
        // Nama role mengikuti data di database (snake_case), bukan label tampilan.
        if ($this->hasRole('super_admin')) {
            return true;
        }

        // Staf Inventaris mengelola aset & inventaris, Auditor hanya membaca audit trail
        return $this->hasRole(['staf_inventaris', 'auditor']);
    }

    public function getFilamentAvatarUrl(): ?string
    {

        return $this->avatar_url ? Storage::url($this->avatar_url) : null;
    }
}
