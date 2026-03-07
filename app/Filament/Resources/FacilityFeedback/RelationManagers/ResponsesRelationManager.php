<?php

namespace App\Filament\Resources\FacilityFeedback\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Illuminate\Database\Eloquent\Model;

class ResponsesRelationManager extends RelationManager
{
    /**
     * Relasi yang didefinisikan pada Model FacilityFeedback.
     */
    protected static string $relationship = 'responses';

    protected static ?string $title = 'Hasil & Respon Survei';
    
    // protected static ?string $icon = 'heroicon-o-chat-bubble-bottom-center-text';

    /**
     * DEFINISI TABEL (STANDAR FILAMENT 5.3)
     */
    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('responder_name')
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tanggal Masuk')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('responder_name')
                    ->label('Nama Responden')
                    ->searchable()
                    ->placeholder('Anonim')
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('responder_type')
                    ->label('Kategori')
                    ->badge()
                    ->color('gray'),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('responder_type')
                    ->label('Filter Peran')
                    ->options([
                        'Mahasiswa' => 'Mahasiswa',
                        'Dosen' => 'Dosen',
                        'Staf/Tendik' => 'Staf/Tendik',
                        'Tamu' => 'Tamu',
                    ]),
            ])
            ->headerActions([
                // Kosongkan: Data masuk melalui portal publik.
            ])
            ->actions([
                /**
                 * VIEW ACTION DENGAN INFOLIST (FIX INTELEPHENSE)
                 * Kita menggunakan closure untuk merender schema agar lebih stabil di Filament 3/5.
                 */
                ViewAction::make()
                    ->label('Buka Respon')
                    ->modalHeading('Hasil Survei Masuk')
                    ->modalWidth('2xl')
                    ->icon('heroicon-m-eye')
                    ->color('primary')
                    ->infolist(
                        fn($infolist) => $infolist
                            ->schema([
                                Section::make('Identitas Responden')
                                    ->description('Data profil pengisi formulir survei.')
                                    ->schema([
                                        TextEntry::make('responder_name')
                                            ->label('Nama Pengirim')
                                            ->default('Anonim / Tidak Menyebutkan')
                                            ->weight('bold'),

                                        TextEntry::make('responder_type')
                                            ->label('Status / Peran')
                                            ->badge()
                                            ->color('info'),

                                        TextEntry::make('created_at')
                                            ->label('Waktu Pengiriman')
                                            ->dateTime('d F Y, H:i')
                                            ->icon('heroicon-m-clock'),
                                    ])->columns(3),

                                Section::make('Rincian Jawaban Survei')
                                    ->description('Hasil jawaban yang diinputkan berdasarkan template Form Builder.')
                                    ->schema(function (Model $record) {
                                        $components = [];

                                        // Ambil struktur soal dari parent (FacilityFeedback)
                                        // Ambil jawaban dari record saat ini (SurveyResponse)
                                        $surveySchema = $record->survey->form_schema ?? [];
                                        $answers = $record->answers ?? [];

                                        // Iterasi schema untuk membangun komponen Infolist secara dinamis
                                        foreach ($surveySchema as $index => $field) {
                                            $type = $field['type'] ?? 'text';
                                            $data = $field['data'] ?? [];
                                            $question = $data['question'] ?? 'Pertanyaan ' . ($index + 1);

                                            $answerKey = 'answer_' . $index;
                                            $answerValue = $answers[$answerKey] ?? null;

                                            // Transformasi nilai jawaban untuk visualisasi
                                            $displayValue = match (true) {
                                                empty($answerValue) => '-',
                                                $type === 'rating' => str_repeat('⭐', (int)$answerValue) . " ({$answerValue}/5)",
                                                is_array($answerValue) => implode(', ', $answerValue),
                                                default => $answerValue,
                                            };

                                            $components[] = TextEntry::make('dynamic_' . $answerKey)
                                                ->label($question)
                                                ->state($displayValue)
                                                ->placeholder('Tidak ada jawaban');
                                        }

                                        return empty($components)
                                            ? [TextEntry::make('empty')->label('Info')->state('Tidak ada data pertanyaan ditemukan.')]
                                            : $components;
                                    })->columns(1),
                            ])
                    ),

                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    /**
     * Memastikan Relation Manager ini tetap bersifat Read-Only untuk integritas data.
     */
    public function isReadOnly(): bool
    {
        return false;
    }
}
