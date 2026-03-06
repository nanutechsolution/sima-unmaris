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
        Schema::create('departments', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // Contoh: FT, FEB, REKTORAT
            $table->string('name'); // Contoh: Fakultas Teknik
            $table->text('description')->nullable();
            // Relasi ke tabel users untuk Kepala Departemen / Dekan
            $table->foreignUuid('manager_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('departments');
    }
};
