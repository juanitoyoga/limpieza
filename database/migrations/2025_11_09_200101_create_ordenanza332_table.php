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
        Schema::create('ordenanza332', function (Blueprint $table) {
            $table->id();

                $table->string('codigo')->unique(); // Ej: INF-001
                $table->string('tipo'); // Ej: "Mala disposición de residuos"
                $table->text('descripcion'); // Detalle de la infracción
                $table->string('nivel_gravedad')->nullable(); // Ej: Leve, Grave, Muy grave
                $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ordenanza332');
    }
};
