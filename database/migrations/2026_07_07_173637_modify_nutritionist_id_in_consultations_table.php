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
        Schema::table('consultations', function (Blueprint $table) {
            // 1. Hapus foreign key yang lama terlebih dahulu
            $table->dropForeign(['nutritionist_id']);
        });

        Schema::table('consultations', function (Blueprint $table) {
            // 2. Ubah kolom menjadi nullable
            $table->unsignedBigInteger('nutritionist_id')->nullable()->change();

            // 3. Pasang kembali foreign key dengan aturan onDelete('set null')
            $table->foreign('nutritionist_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('consultations', function (Blueprint $table) {
            $table->dropForeign(['nutritionist_id']);
        });

        Schema::table('consultations', function (Blueprint $table) {
            // Kembalikan ke kondisi semula (tidak nullable) jika di-rollback
            $table->unsignedBigInteger('nutritionist_id')->nullable(false)->change();
            $table->foreign('nutritionist_id')->references('id')->on('users');
        });
    }
};
