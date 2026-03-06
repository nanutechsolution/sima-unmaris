<?php

namespace App\Filament\Resources\Categories\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Detail Kategori')
                    ->description('Kelola pengelompokan jenis aset kampus')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nama Kategori')
                            ->required()
                            ->maxLength(100)
                            ->placeholder('Contoh: Elektronik / Furnitur'),

                        TextInput::make('prefix_code')
                            ->label('Kode Prefix')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(10)
                            ->placeholder('Contoh: ELK')
                            ->helperText('Digunakan sebagai awalan untuk auto-generate kode aset.'),

                        Textarea::make('description')
                            ->label('Deskripsi')
                            ->maxLength(65535)
                            ->columnSpanFull(),
                    ])->columns(2),
            ]);
    }
}
