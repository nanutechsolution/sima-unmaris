<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('maintenance_logs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            
            // Relasi ke tabel Aset
            $table->foreignUuid('asset_id')->constrained('assets')->cascadeOnDelete();
            
            // Detail Perbaikan
            $table->date('maintenance_date');
            $table->string('problem_description');
            $table->text('action_taken')->nullable();
            $table->decimal('cost', 15, 2)->default(0);
            
            // Siapa yang memperbaiki? Bisa nama bengkel, teknisi internal, atau vendor
            $table->string('performed_by')->nullable(); 
            
            // Status perbaikan
            $table->string('status', 30)->default('completed'); // scheduled, in_progress, completed
            
            // Bukti nota / kuitansi perbaikan
            $table->string('receipt_photo_path')->nullable();
            
            $table->text('notes')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('maintenance_logs');
    }
};