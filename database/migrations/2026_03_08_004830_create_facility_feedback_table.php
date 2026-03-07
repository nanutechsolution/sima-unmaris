<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('facility_feedbacks', function (Blueprint $table) {
            $table->uuid('id')->primary();
            
            // Kategori fasilitas yang dinilai (Cth: WiFi, Kebersihan, Keamanan, Pelayanan)
            $table->string('category')->index(); 
            
            // Bintang kepuasan (1 sampai 5)
            $table->tinyInteger('rating'); 
            
            // Komentar atau saran detail
            $table->text('comments')->nullable();
            
            // Identitas pelapor (Bisa dikosongkan jika ingin anonim)
            $table->string('reporter_name')->nullable();
            
            // Jenis pelapor: Mahasiswa, Dosen, Staf, Tamu
            $table->string('reporter_type')->nullable(); 
            
            // Status tindak lanjut oleh pihak kampus
            $table->string('status')->default('unread'); // unread, reviewed, followed_up
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('facility_feedbacks');
    }
};