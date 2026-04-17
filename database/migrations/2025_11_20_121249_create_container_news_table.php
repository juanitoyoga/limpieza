<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('containernews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('container_id')
                  ->constrained('containers')
                  ->onDelete('cascade'); // relación con el contenedor padre
            $table->foreignId('barrio_id')
                  ->nullable()
                  ->constrained('barrios')
                  ->onDelete('set null'); // relación con barrio
            $table->string('title'); // título de la noticia
            $table->string('author')->nullable(); // autor
            $table->text('body'); // texto principal
            $table->text('references')->nullable(); // fuentes o referencias
            $table->boolean('verified')->default(false); // verificación
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('containernews');
    }
};
