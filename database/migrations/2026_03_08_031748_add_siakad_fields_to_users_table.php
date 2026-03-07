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
        // 1. Menambahkan kolom identitas SIAKAD ke tabel users
        Schema::table('users', function (Blueprint $table) {
            $table->string('username_siakad')->nullable()->unique()->after('email');
            $table->string('role_name')->nullable()->after('username_siakad');
        });

        // 2. Menambahkan kolom relasi User dan Audit IP ke tabel survey_responses
        // Ini untuk memperbaiki error "Unknown column 'user_id'"
        if (Schema::hasTable('survey_responses')) {
            Schema::table('survey_responses', function (Blueprint $table) {
                $table->foreignUuid('user_id')->nullable()->after('facility_feedback_id')->constrained('users')->nullOnDelete();
                $table->string('user_agent')->nullable()->after('ip_address');
            });
        }

        /**
         * 3. PERBAIKAN SANCTUM (CRITICAL)
         * Mengubah kolom tokenable_id pada tabel personal_access_tokens 
         * dari BigInt menjadi UUID agar sinkron dengan tabel users.
         */
        if (Schema::hasTable('personal_access_tokens')) {
            Schema::table('personal_access_tokens', function (Blueprint $table) {
                $table->uuid('tokenable_id')->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Kembalikan ke BigInt jika rollback
        if (Schema::hasTable('personal_access_tokens')) {
            Schema::table('personal_access_tokens', function (Blueprint $table) {
                $table->bigInteger('tokenable_id')->change();
            });
        }

        if (Schema::hasTable('survey_responses')) {
            Schema::table('survey_responses', function (Blueprint $table) {
                $table->dropForeign(['user_id']);
                $table->dropColumn(['user_id', 'ip_address', 'user_agent']);
            });
        }

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['username_siakad', 'role_name']);
        });
    }
};
