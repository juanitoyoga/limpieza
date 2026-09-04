<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ejecutar la migración.
     */
    public function up(): void
    {
        Schema::create('service_scopes', function (Blueprint $table) {
            $table->id();

            $table->string('code', 30)
                ->unique()
                ->comment('Código único del ámbito');

            $table->string('name', 150)
                ->unique()
                ->comment('Nombre del ámbito');

            $table->string('scope_type', 50)
                ->index()
                ->comment('Categoría general del ámbito');

            $table->text('description')
                ->nullable()
                ->comment('Descripción del ámbito');

            $table->unsignedInteger('sort_order')
                ->default(0)
                ->comment('Orden de presentación');

            $table->boolean('active')
                ->default(true)
                ->index()
                ->comment('Indica si el ámbito está disponible');

            $table->timestamps();
        });
    }

    /**
     * Revertir la migración.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_scopes');
    }
};
