<?php

namespace App\Filament\Resources\Permissions\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PermissionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Detail Hak Akses')
                    ->description('Daftarkan kata kunci hak akses sistem.')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nama Hak Akses (Gunakan huruf kecil & underscore)')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255)
                            ->placeholder('Contoh: view_asset, create_asset, delete_user'),
                    ])->columns(1),
            ]);
    }
}
