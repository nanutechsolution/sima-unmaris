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
       Schema::create('stock_opnames', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('title'); // Contoh: "Audit Semester Ganjil 2026"
            $table->date('start_date');
            $table->date('end_date');
            $table->string('status')->default('in_progress'); // in_progress, completed
            $table->foreignUuid('pic_id')->constrained('users')->restrictOnDelete(); // Ketua Tim Auditor
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_opnames');
    }
};
