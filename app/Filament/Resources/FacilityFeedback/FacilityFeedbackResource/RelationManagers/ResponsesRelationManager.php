<?php

namespace App\Filament\Resources\FacilityFeedback\RelationManagers;

use App\Enums\AssetConditionEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section as ComponentsSection;
use Illuminate\Database\Eloquent\Model;

class ResponsesRelationManager extends RelationManager
{
    /**
     * Nama relasi yang didefinisikan pada model FacilityFeedback
     */
    protected static string $relationship = 'responses';

    protected static ?string $title = 'Hasil & Respon Survei';

    // protected static ?string $icon = 'heroicon-o-chat-bubble-bottom-center-text';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('responder_name')
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Waktu Masuk')
                    ->dateTime('d M Y - H:i')
                    ->sortable(),

                Tables\Columns\TextColumn::make('responder_name')
                    ->label('Nama Responden')
                    ->searchable()
                    ->placeholder('Anonim')
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('responder_type')
                    ->label('Tipe Pengguna')
                    ->badge()
                    ->color('info'),
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
                // Header action 'Create' biasanya dikosongkan karena data 
                // masuk dari portal publik, bukan diinput manual oleh admin.
            ])
            ->actions([
                /**
                 * VIEW ACTION: Mengonversi data JSON menjadi tampilan visual.
                 * Kita membedah form_schema dari parent dan mencocokkannya dengan 
                 * kolom answers pada record SurveyResponse.
                 */
                ViewAction::make()
                    ->label('Lihat Jawaban')
                    ->modalHeading('Detail Jawaban Responden')
                    ->modalWidth('2xl')
                    ->infolist([
                        ComponentsSection::make('Identitas Responden')
                            ->schema([
                                TextEntry::make('responder_name')
                                    ->label('Nama')
                                    ->default('Anonim'),
                                TextEntry::make('responder_type')
                                    ->label('Status / Peran'),
                                TextEntry::make('created_at')
                                    ->label('Waktu Submit')
                                    ->dateTime('d F Y, H:i'),
                            ])->columns(3),

                        ComponentsSection::make('Rincian Jawaban Survei')
                            ->description('Hasil jawaban berdasarkan komponen yang dirakit di Form Builder.')
                            ->schema(function (Model $record) {
                                $components = [];

                                // Ambil struktur soal dari induknya (FacilityFeedback)
                                $surveySchema = $record->survey->form_schema ?? [];
                                // Ambil jawaban dari tabel response ini
                                $answers = $record->answers ?? [];

                                // Looping untuk menjodohkan Soal dan Jawaban dari JSON
                                foreach ($surveySchema as $index => $field) {
                                    $type = $field['type'] ?? 'text';
                                    $data = $field['data'] ?? [];
                                    $question = $data['question'] ?? 'Pertanyaan ' . ($index + 1);

                                    $answerKey = 'answer_' . $index;
                                    $answerValue = $answers[$answerKey] ?? '-';

                                    // Transformasi visual untuk Rating Bintang
                                    if ($type === 'rating' && is_numeric($answerValue)) {
                                        $answerValue = str_repeat('⭐', (int) $answerValue) . " ({$answerValue}/5)";
                                    }
                                    // Transformasi untuk pilihan ganda/array
                                    elseif (is_array($answerValue)) {
                                        $answerValue = implode(', ', $answerValue);
                                    }

                                    $components[] = TextEntry::make('dynamic_answer_' . $index)
                                        ->label($question)
                                        ->state($answerValue);
                                }

                                return empty($components)
                                    ? [TextEntry::make('empty')->label('Info')->state('Tidak ada jawaban.')]
                                    : $components;
                            })->columns(1),
                    ]),

                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
