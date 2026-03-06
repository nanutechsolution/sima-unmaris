<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('asset_loans', function (Blueprint $table) {
            $table->uuid('id')->primary();
            
            // Relasi ke Aset dan Peminjam
            $table->foreignUuid('asset_id')->constrained('assets')->cascadeOnDelete();
            $table->foreignUuid('borrower_user_id')->constrained('users')->restrictOnDelete();
            
            // Waktu Pinjam & Tenggat Kembali
            $table->dateTime('loan_date');
            $table->dateTime('expected_return_date');
            $table->dateTime('actual_return_date')->nullable(); // Diisi saat barang benar-benar dikembalikan
            
            $table->text('notes')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asset_loans');
    }
};