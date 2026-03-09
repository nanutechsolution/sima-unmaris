<?php

namespace App\Filament\Pages\Auth;

use Filament\Auth\Pages\EditProfile as BaseEditProfile;
use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable;
use Filament\Facades\Filament;

class EditProfile extends BaseEditProfile
{
    /**
     * OVERRIDE: Memastikan sistem mengambil User Model yang tepat.
     * Ini mengatasi bug/konflik antara guard 'web' (Filament) dan 'sanctum' (React API).
     */
    public function getUser(): Authenticatable & Model
    {
        // 1. Coba ambil dari Filament, jika null, paksa ambil dari sistem auth bawaan Laravel
        $user = Filament::auth()->user() ?? auth('web')->user() ?? auth()->user();

        // 2. Jika masih null, berarti sesi benar-benar habis, berikan pesan yang jelas
        if (! $user instanceof Model) {
            throw new \LogicException('Sesi login Anda tidak terbaca atau telah berakhir. Silakan muat ulang halaman (Refresh/F5) atau login kembali.');
        }

        return $user;
    }

    /**
     * Memodifikasi form profil bawaan untuk menambahkan fitur Upload Foto
     */
    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                FileUpload::make('avatar_url')
                    ->label('Foto Profil')
                    ->avatar()           // Membuat tampilannya bundar
                    ->imageEditor()      // Mengizinkan user mengedit/memutar foto
                    ->circleCropper()    // Memaksa hasil crop berbentuk lingkaran
                    ->directory('avatars') // Disimpan di storage/app/public/avatars
                    ->maxSize(2048)      // Maksimal 2MB
                    ->columnSpanFull()
                    ->alignCenter(),
                    
                // Mengambil komponen bawaan Filament sesuai core file Anda
                $this->getNameFormComponent(),
                $this->getEmailFormComponent(),
                $this->getPasswordFormComponent(),
                $this->getPasswordConfirmationFormComponent(),
                $this->getCurrentPasswordFormComponent(),
            ]);
    }
}