<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migrasi ini di server SIAKAD.
     */
    public function up(): void
    {
        if (Schema::hasTable('personal_access_tokens')) {
            Schema::table('personal_access_tokens', function (Blueprint $table) {
                // 1. Ubah tokenable_id menjadi UUID agar bisa menampung ID User
                $table->uuid('tokenable_id')->change();
                
                // 2. Pastikan ID utama tetap auto_increment (seringkali bermasalah saat change)
                $table->bigIncrements('id')->change();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('personal_access_tokens')) {
            Schema::table('personal_access_tokens', function (Blueprint $table) {
                $table->bigInteger('tokenable_id')->change();
            });
        }
    }
};