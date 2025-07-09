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
        Schema::create('room_types', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // estándar, premium, VIP
            $table->decimal('base_price', 10, 2); // precio base por noche
            $table->integer('max_capacity'); // capacidad máxima de personas
            $table->text('description')->nullable(); // descripción del tipo de habitación
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('room_types');
    }
};
