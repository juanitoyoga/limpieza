<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nominations', function (Blueprint $table) {
            $table->id();

            // QUIÉN ENVÍA EL TRÁMITE
            $table->foreignId('nominator_id')
                  ->constrained('users')
                  ->onDelete('cascade');

            // PERSONA NOMINADA
            $table->foreignId('candidate_user_id')
                  ->constrained('users')
                  ->onDelete('cascade');

            // CARGO ASOCIADO AL ROL
            $table->foreignId('role_id')
                  ->nullable()
                  ->constrained('user_roles')
                  ->onDelete('set null');

            // INFORMACIÓN DEL DOCUMENTO DE NOMBRAMIENTO
            $table->enum('issuer_type', ['JUNTA_PARROQUIAL', 'DMQ'])
                  ->comment('Entidad que emite el documento oficial');

            $table->string('released_by')->nullable()
                  ->comment('Persona o institución que emite el documento');

            $table->string('document_path')->nullable()
                  ->comment('Ruta del PDF o imagen del nombramiento');

            // FECHAS DEL DOCUMENTO
            $table->date('fecha_emision')->nullable();
            $table->date('fecha_inicio_vigencia')->nullable();
            $table->date('fecha_fin_vigencia')->nullable();

            // ESTADOS DEL PROCESO
            $table->enum('estado', [
                'propuesta',
                'verificada',
                'aprobada',
                'rechazada',
                'expirada'
            ])->default('propuesta');

            $table->text('observaciones')->nullable();

            // CONTROL DE APROBACIÓN
            $table->foreignId('approved_by')
                  ->nullable()
                  ->constrained('users')
                  ->onDelete('set null');

            $table->timestamp('verified_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('rejected_at')->nullable();

            // INFORMACIÓN TÉCNICA
            $table->string('hash_reference')->nullable();
            $table->unsignedInteger('version')->default(1);
            $table->string('numero_tramite')->unique();

            // ESTADO ACTUAL DEL CARGO ASOCIADO A LA NOMINACIÓN
            $table->boolean('is_active')->default(false);

            $table->timestamps();

            // ÍNDICES PARA BÚSQUEDAS RÁPIDAS
            $table->index(['estado']);
            $table->index(['candidate_user_id']);
            $table->index(['role_id']);
            $table->index(['numero_tramite']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nominations');
    }
};
