<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('assets', function (Blueprint $table) {
            // Menambahkan kolom Umur Ekonomis (default 5 tahun untuk aset IT/Kampus)
            $table->integer('useful_life_years')->default(5)->after('acquisition_date')->comment('Umur ekonomis dalam tahun');
        });
    }

    public function down(): void
    {
        Schema::table('assets', function (Blueprint $table) {
            $table->dropColumn('useful_life_years');
        });
    }
};