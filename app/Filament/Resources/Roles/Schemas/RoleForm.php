<?php

namespace App\Filament\Resources\Roles\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class RoleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
               Section::make('Detail Role')
                    ->description('Tentukan nama role dan hak akses yang diberikan.')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nama Role')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255)
                            ->placeholder('Contoh: Super Admin, Staff Gudang'),
                            
                        // Filament 5 secara cerdas bisa membaca relasi Spatie Permission secara otomatis!
                        Select::make('permissions')
                            ->multiple()
                            ->relationship('permissions', 'name')
                            ->preload()
                            ->label('Hak Akses (Permissions)')
                            ->searchable()
                            ->helperText('Pilih tindakan apa saja yang boleh dilakukan oleh role ini.'),
                    ])->columns(1),
            ]);
    }
}
