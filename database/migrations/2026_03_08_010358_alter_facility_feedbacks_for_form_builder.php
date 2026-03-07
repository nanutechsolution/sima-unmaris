<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('facility_feedbacks', function (Blueprint $table) {
            // Hapus kolom lama yang sudah tidak dipakai
            $table->dropColumn(['category', 'rating', 'comments', 'reporter_name', 'reporter_type']);
            
            // Tambahkan kolom baru untuk Form Builder
            $table->string('title')->after('id');
            $table->text('description')->nullable()->after('title');
            $table->json('form_schema')->nullable()->after('description');
        });
    }

    public function down(): void
    {
        Schema::table('facility_feedbacks', function (Blueprint $table) {
            // Rollback: Hapus kolom baru
            $table->dropColumn(['title', 'description', 'form_schema']);
            
            // Rollback: Kembalikan kolom lama (tipe data disesuaikan dengan yang lama)
            $table->string('category')->nullable();
            $table->integer('rating')->nullable();
            $table->text('comments')->nullable();
            $table->string('reporter_name')->nullable();
            $table->string('reporter_type')->nullable();
        });
    }
};