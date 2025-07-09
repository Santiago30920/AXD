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
        Schema::create('temporate', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hotel_id')->constrained('hotels')->onDelete('cascade');
            $table->enum('season', ['alta', 'baja']); // temporada alta o baja
            $table->date('start_date');
            $table->date('end_date');
            $table->decimal('price_multiplier', 3, 2)->default(1.00); // multiplicador de precio
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('temporate');
    }
};
