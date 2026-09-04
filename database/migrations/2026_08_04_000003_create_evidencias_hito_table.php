<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('evidencias_hito', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique(); // idempotencia de sync, igual que en hitos

            $table->foreignId('hito_id')->constrained('hitos_contrato_servicio')->cascadeOnDelete();

            $table->enum('tipo', ['ANTES', 'DESPUES']);
            $table->enum('formato', ['FOTO', 'VIDEO']);

            $table->string('ruta_archivo');
            $table->uuid('media_uuid')->nullable(); // referencia a media_uploads.uuid

            $table->decimal('latitud', 10, 7)->nullable();
            $table->decimal('longitud', 10, 7)->nullable();

            $table->foreignId('user_id')->constrained(); // contratista que capturó
            $table->timestamp('capturado_en_campo_at');
            $table->timestamp('sincronizado_at')->nullable();

            $table->timestamps();

            $table->index(['hito_id', 'tipo']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('evidencias_hito');
    }
};
