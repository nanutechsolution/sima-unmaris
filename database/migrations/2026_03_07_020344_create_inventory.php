<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. MASTER: Barang Habis Pakai (ATK)
        Schema::create('inventory_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('sku')->unique(); // Stock Keeping Unit (Cth: ATK-HVS-A4)
            $table->string('name');
            $table->string('category')->index(); // Cth: Alat Tulis, Kebersihan, Konsumsi
            $table->string('unit'); // Cth: Rim, Box, Pcs, Pack
            $table->integer('current_stock')->default(0);
            $table->integer('min_stock')->default(5); // Ambang batas peringatan stok rendah
            $table->text('description')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // 2. TRANSAKSI: Stok Masuk & Keluar
        Schema::create('inventory_transactions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('inventory_item_id')->constrained()->cascadeOnDelete();

            // Type: 'in' (Pembelian/Masuk), 'out' (Permintaan Unit/Keluar)
            $table->string('type', 10);
            $table->integer('quantity');

            // Relasi ke Unit Kerja yang meminta (jika stok keluar)
            $table->foreignUuid('department_id')
                ->nullable()
                ->constrained('departments')
                ->nullOnDelete();
            // Aktor yang memproses
            $table->foreignUuid('user_id')->constrained('users');

            $table->date('transaction_date');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_transactions');
        Schema::dropIfExists('inventory_items');
    }
};
