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
        Schema::create('driver_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('vehicle_plate_number')->nullable(); // Plat Nomor Kendaraan Kurir
            $table->enum('status', ['available', 'delivering', 'offing'])->default('available'); // Status kurir
            $table->string('delivery_zone')->nullable(); // Wilayah tugas (misal: Bandung Utara, Bandung Selatan)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('driver_details');
    }
};
