<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Tabel Detail Aset yang di-Audit (Sensus)
        Schema::create('stock_opname_details', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('stock_opname_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('asset_id')->constrained()->cascadeOnDelete();

            // Hasil Audit di Lapangan
            $table->boolean('is_found')->default(true); // Apakah barangnya ada?
            $table->string('actual_condition')->nullable(); // Kondisi riil saat ini
            $table->foreignUuid('actual_room_id')->nullable()->constrained('rooms'); // Ruangan riil saat ditemukan

            $table->text('notes')->nullable(); // Keterangan (Cth: "Barang dipindah tanpa izin")
            $table->timestamp('scanned_at')->nullable(); // Waktu di-scan
            $table->foreignUuid('scanned_by')->nullable()->constrained('users'); // Siapa auditor yang ngecek

            $table->timestamps();

            // Mencegah 1 aset diaudit 2 kali dalam sesi yang sama
            $table->unique(['stock_opname_id', 'asset_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_opname_details');
    }
};
