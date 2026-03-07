<?php

namespace App\Filament\Resources\FacilityFeedback\Schemas;

use Filament\Forms\Components\Builder;
use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class FacilityFeedbackForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Pengaturan Identitas Survei')
                    ->schema([
                        TextInput::make('title')
                            ->label('Judul Survei / Form')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Contoh: Survei Kepuasan Fasilitas Lab Komputer'),

                        Select::make('status')
                            ->label('Status Publikasi')
                            ->options([
                                'active' => 'Aktif (Bisa diakses publik)',
                                'draft' => 'Draft (Belum dipublish)',
                            ])
                            ->default('draft')
                            ->required(),

                        Textarea::make('description')
                            ->label('Deskripsi Singkat')
                            ->placeholder('Jelaskan tujuan survei ini...')
                            ->columnSpanFull(),
                    ])->columns(2),

                // --- JANTUNG DARI FORM BUILDER ---
                Section::make('Rancang Pertanyaan (Form Builder)')
                    ->description('Tambahkan dan susun pertanyaan survei sesuai kebutuhan Anda layaknya Google Forms.')
                    ->schema([
                        Builder::make('form_schema')
                            ->label('') // Label dikosongkan karena sudah ada judul section
                            ->blocks([
                                // Blok 1: Rating Bintang
                                Block::make('rating')
                                    ->label('Rating Bintang')
                                    ->icon('heroicon-m-star')
                                    ->schema([
                                        TextInput::make('question')
                                            ->label('Pertanyaan')
                                            ->required()
                                            ->placeholder('Cth: Seberapa puas Anda dengan AC di ruangan ini?'),
                                        Toggle::make('is_required')
                                            ->label('Wajib Diisi')
                                            ->default(true),
                                    ]),

                                // Blok 2: Teks Singkat
                                Block::make('text')
                                    ->label('Jawaban Singkat')
                                    ->icon('heroicon-m-bars-3-bottom-left')
                                    ->schema([
                                        TextInput::make('question')
                                            ->label('Pertanyaan')
                                            ->required(),
                                        Toggle::make('is_required')
                                            ->label('Wajib Diisi')
                                            ->default(true),
                                    ]),

                                // Blok 3: Teks Panjang (Komentar)
                                Block::make('textarea')
                                    ->label('Komentar / Paragraf')
                                    ->icon('heroicon-m-bars-3')
                                    ->schema([
                                        TextInput::make('question')
                                            ->label('Pertanyaan')
                                            ->required(),
                                        Toggle::make('is_required')
                                            ->label('Wajib Diisi')
                                            ->default(false),
                                    ]),

                                // Blok 4: Pilihan Ganda (Dropdown)
                                Block::make('select')
                                    ->label('Pilihan Ganda (Dropdown)')
                                    ->icon('heroicon-m-chevron-down')
                                    ->schema([
                                        TextInput::make('question')
                                            ->label('Pertanyaan')
                                            ->required(),
                                        TagsInput::make('options')
                                            ->label('Opsi Pilihan (Ketik lalu Enter)')
                                            ->required()
                                            ->placeholder('Cth: Sangat Baik, Baik, Buruk'),
                                        Toggle::make('is_required')
                                            ->label('Wajib Diisi')
                                            ->default(true),
                                    ]),
                            ])
                            ->addActionLabel('Tambah Pertanyaan Baru')
                            ->collapsible()
                            ->cloneable()
                            ->reorderableWithButtons() // Memudahkan admin menggeser urutan pertanyaan
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
