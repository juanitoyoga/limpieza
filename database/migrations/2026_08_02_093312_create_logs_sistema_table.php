<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('logs_sistema', function (Blueprint $table) {
            $table->id();

            // Clase/servicio que generó el log (ej: App\Livewire\Operacion\Resoluciones\Aprobar)
            $table->string('origen');

            // Categoría del evento (ej: livewire_bloqueo_acceso, job_exception, blockchain_error)
            $table->string('tipo_origen');

            // Severidad: info | warning | error | critical
            $table->string('nivel')->default('info');

            // Título corto, legible en la tabla del panel
            $table->string('comentario');

            // Detalle largo: puede ser JSON serializado, stack trace, o texto libre
            $table->longText('mensaje_error')->nullable();

            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('ip')->nullable();

            $table->timestamps();

            $table->index(['nivel', 'tipo_origen']);
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('logs_sistema');
    }
};
