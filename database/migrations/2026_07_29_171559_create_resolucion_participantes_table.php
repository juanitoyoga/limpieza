<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('resolucion_participantes', function (Blueprint $table) {
            $table->id();

            $table->foreignId('resolucion_id')
                ->constrained('resoluciones')
                ->cascadeOnDelete();

            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->string('nombre_firmante');
            $table->string('documento_identidad', 30)->nullable();
            $table->string('cargo')->nullable();
            $table->unsignedInteger('orden_firma')->default(0);

            $table->timestamps();

            $table->index('user_id');
            $table->index('orden_firma');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('resolucion_participantes');
    }
};
