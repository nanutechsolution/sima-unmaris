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
        // 1. PERBAIKAN TABEL SURVEY RESPONSES
        if (Schema::hasTable('survey_responses')) {
            Schema::table('survey_responses', function (Blueprint $table) {
                /**
                 * Cek apakah kolom user_id sudah ada sebelum mencoba menambahkannya.
                 * Ini mencegah error "Duplicate column name" jika migrasi dijalankan ulang.
                 */
                if (!Schema::hasColumn('survey_responses', 'user_id')) {
                    $table->foreignUuid('user_id')
                        ->nullable()
                        ->after('facility_feedback_id')
                        ->constrained('users')
                        ->nullOnDelete();
                    
                    $table->index('user_id');
                }
            });
        }

        // 2. PERBAIKAN TABEL SANCTUM (PENTING)
        // Memperbaiki error "Field 'id' doesn't have a default value"
        if (Schema::hasTable('personal_access_tokens')) {
            Schema::table('personal_access_tokens', function (Blueprint $table) {
                // Mengembalikan sifat auto_increment pada kolom id
                $table->bigIncrements('id')->change();
                
                // Memastikan tokenable_id adalah UUID agar sinkron dengan tabel users kita
                if (Schema::hasColumn('personal_access_tokens', 'tokenable_id')) {
                    $table->uuid('tokenable_id')->change();
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('personal_access_tokens')) {
            Schema::table('personal_access_tokens', function (Blueprint $table) {
                $table->bigInteger('id')->change();
            });
        }

        if (Schema::hasTable('survey_responses')) {
            Schema::table('survey_responses', function (Blueprint $table) {
                if (Schema::hasColumn('survey_responses', 'user_id')) {
                    $table->dropForeign(['user_id']);
                    $table->dropColumn('user_id');
                }
            });
        }
    }
};