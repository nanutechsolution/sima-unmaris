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
        // 1. MASTER DATA: Kategori Aset
        Schema::create('categories', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name', 100);
            $table->string('prefix_code', 10)->unique(); // Cth: 'KMP' untuk Komputer (Berguna untuk generate kode unik aset)
            $table->text('description')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // 2. MASTER DATA: Lokasi Kampus
        Schema::create('locations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name', 150); // Cth: Kampus Pusat Sumba Barat
            $table->text('address')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // 3. MASTER DATA: Ruangan
        Schema::create('rooms', function (Blueprint $table) {
            $table->uuid('id')->primary();
            // cascadeOnDelete: Jika lokasi dihapus, ruangan ikut terhapus
            $table->foreignUuid('location_id')->constrained('locations')->cascadeOnDelete();
            $table->string('name', 100); // Cth: Lab Komputer A
            $table->string('code', 50)->unique();
            $table->timestamps();
            $table->softDeletes();
        });

        // 4. MASTER DATA: Supplier / Vendor
        Schema::create('suppliers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name', 150);
            $table->string('contact_person', 100)->nullable();
            $table->string('phone', 20)->nullable();
            $table->text('address')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // 5. CORE SYSTEM: Manajemen Aset
        Schema::create('assets', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('asset_code', 50)->unique()->index(); // Identity Asset (Cth: UNMARIS/IT/2026/001)
            $table->string('name', 200);
            
            // Relasi UUID Master Data (restrictOnDelete agar histori audit tidak rusak jika master dihapus)
            $table->foreignUuid('category_id')->constrained('categories')->restrictOnDelete();
            $table->foreignUuid('room_id')->constrained('rooms')->restrictOnDelete();
            $table->foreignUuid('supplier_id')->nullable()->constrained('suppliers')->nullOnDelete();
            
            // Penanggung Jawab (Relasi ke tabel users yang sudah dikonversi ke UUID)
            $table->foreignUuid('pic_user_id')->constrained('users')->restrictOnDelete(); 
            
            // Financial & Lifecycle
            $table->decimal('acquisition_value', 15, 2);
            $table->date('acquisition_date');
            
            // Menggunakan tipe data String untuk kompatibilitas Enum di layer Aplikasi
            $table->string('status', 30)->index(); // Available, In Use, Maintenance, Lost, Retired
            $table->string('condition', 30); // Good, Fair, Damaged
            
            // Security & QR Code
            $table->string('qr_signature_hash')->nullable()->unique(); // Digital Signature Aset
            
            $table->timestamps();
            $table->softDeletes(); // Wajib ada untuk mematuhi Audit Compliance
        });

        // 6. CORE FEATURE: Proses Penyerahan / Serah Terima Aset
        Schema::create('asset_handovers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('asset_id')->constrained('assets')->cascadeOnDelete();
            
            // Aktor Transaksi (Relasi ke users ber-UUID)
            $table->foreignUuid('giver_user_id')->constrained('users')->restrictOnDelete();
            $table->foreignUuid('receiver_user_id')->constrained('users')->restrictOnDelete();
            
            // Metadata Serah Terima
            $table->timestamp('handover_time')->useCurrent(); // Server timestamp yang valid
            $table->foreignUuid('location_id')->nullable()->constrained('locations')->restrictOnDelete(); // Lokasi penyerahan fisik
            
            // Digital Proofs (Storage Strategy: Simpan PATH-nya saja, file ada di disk/S3)
            $table->string('asset_photo_path');
            $table->string('document_photo_path'); // Foto Berita Acara
            
            $table->text('notes')->nullable();
            $table->string('digital_signature_hash')->nullable(); // Untuk eskalasi legal
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop dengan urutan terbalik dari relasi untuk mencegah foreign key error
        Schema::dropIfExists('asset_handovers');
        Schema::dropIfExists('assets');
        Schema::dropIfExists('suppliers');
        Schema::dropIfExists('rooms');
        Schema::dropIfExists('locations');
        Schema::dropIfExists('categories');
    }
};