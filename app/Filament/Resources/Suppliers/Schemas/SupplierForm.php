<?php

namespace App\Filament\Resources\Suppliers\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class SupplierForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nama Perusahaan / Toko')
                    ->required()
                    ->maxLength(150)
                    ->placeholder('Contoh: PT. Teknologi Nusantara'),

                TextInput::make('contact_person')
                    ->label('Nama Kontak Person')
                    ->maxLength(100)
                    ->placeholder('Contoh: Bpk. Budi Santoso'),

                TextInput::make('phone')
                    ->label('Nomor Telepon / WhatsApp')
                    ->tel()
                    ->maxLength(20)
                    ->placeholder('Contoh: 081234567890'),

                Textarea::make('address')
                    ->label('Alamat Lengkap')
                    ->maxLength(65535)
                    ->columnSpanFull(),
            ])->columns(2);
    }
}
