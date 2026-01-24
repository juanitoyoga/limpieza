<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('containermedia', function (Blueprint $table) {
            $table->id();
            $table->foreignId('container_id')
                  ->constrained('containers')
                  ->onDelete('cascade'); // relación con el contenedor padre
            $table->foreignId('barrio_id')
                  ->nullable()
                  ->constrained('barrios')
                  ->onDelete('set null'); // relación con barrio
            $table->string('image_path')->nullable(); // ruta de la fotografía
            $table->text('text')->nullable(); // texto asociado
            $table->text('label')->nullable(); // titulo superior
            $table->text('footer')->nullable(); // texto al pied puede ser una referencia al autor ...
            $table->integer('order')->default(0); // orden dentro del contenedor
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('containermedia');
    }
};
