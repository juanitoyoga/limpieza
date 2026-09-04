<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('units', function (Blueprint $table) {
            $table->id();
            $table->string('name', 60);          // Ejemplo: Kilómetro
            $table->string('symbol', 15);        // Ejemplo: km
            $table->string('category', 40);      // Ejemplo: Longitud, Peso, Volumen
            $table->string('system', 30);        // Ejemplo: Métrico SI, Imperial
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('units');
    }
};
