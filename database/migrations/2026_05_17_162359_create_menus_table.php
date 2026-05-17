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
        Schema::create('menus', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable(false);
            $table->text('description')->nullable();
            $table->string('image_path')->nullable();
            $table->boolean('is_available')->default(true);
            $table->timestamps();
        });

        Schema::create('menu_nutritions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('menu_id')->constrained()->onDelete('cascade');
            $table->integer('calories')->nullable(false);
            $table->decimal('protein_g', 5, 2)->nullable(false);
            $table->decimal('carbs_g', 5, 2)->nullable(false);
            $table->decimal('fat_g', 5, 2)->nullable(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menus');
    }
};
