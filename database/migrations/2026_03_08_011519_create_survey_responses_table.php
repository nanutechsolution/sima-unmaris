<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('survey_responses', function (Blueprint $table) {
            $table->uuid('id')->primary();
            
            // Relasi ke tabel Template Survei (FacilityFeedback)
            $table->foreignUuid('facility_feedback_id')
                  ->constrained('facility_feedbacks')
                  ->cascadeOnDelete();
                  
            // Identitas Pengisi
            $table->string('responder_name')->nullable();
            $table->string('responder_type'); // Mahasiswa, Dosen, Staf, Tamu
            
            // KUNCI UTAMA: Menyimpan jawaban dinamis dalam format JSON
            $table->json('answers'); 
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('survey_responses');
    }
};