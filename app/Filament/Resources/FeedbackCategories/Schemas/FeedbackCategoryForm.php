<?php

namespace App\Filament\Resources\FeedbackCategories\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class FeedbackCategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Kategori Survei & Masukan')
                    ->description('Buat kategori topik yang bisa dipilih mahasiswa saat mengisi survei.')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nama Kategori Layanan')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->placeholder('Contoh: Jaringan WiFi & Internet'),

                        Textarea::make('description')
                            ->label('Deskripsi Singkat')
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
