<?php

namespace App\Filament\Resources\Departments\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class DepartmentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Organisasi')
                    ->description('Kelola data Fakultas, Prodi, atau Biro Administrasi')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nama Unit / Fakultas')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Contoh: Fakultas Teknik / Biro Umum'),

                        TextInput::make('code')
                            ->label('Kode Unit')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(50)
                            ->placeholder('Contoh: FT / BU'),

                        Select::make('manager_id')
                            ->relationship('manager', 'name')
                            ->label('Kepala Unit / Dekan')
                            ->searchable()
                            ->preload()
                            ->placeholder('Pilih Penanggung Jawab Unit'),

                        Textarea::make('description')
                            ->label('Deskripsi Singkat')
                            ->maxLength(65535)
                            ->columnSpanFull(),
                    ])->columns(2),
            ]);
    }
}
