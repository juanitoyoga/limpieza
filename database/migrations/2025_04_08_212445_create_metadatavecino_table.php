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
        Schema::create('metadata_vecinos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vecino_id')->constrained()->cascadeOnDelete();
            $table->string('tipo'); // Igual que en vecinos.tipo
            $table->json('datos');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('metadatavecino');
    }
};
