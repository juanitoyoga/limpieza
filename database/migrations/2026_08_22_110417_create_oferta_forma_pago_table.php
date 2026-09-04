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
        Schema::create('oferta_forma_pago', function (Blueprint $table) {
            $table->id();
            $table->foreignId('oferta_id')->constrained('ofertas')->cascadeOnDelete();
            $table->unsignedTinyInteger('orden')->default(0); // secuencia del plan
            $table->enum('tipo', ['anticipo', 'contra_servicio', 'saldo_final']);
            $table->foreignId('catalogo_servicio_id')->nullable()->constrained('catalogo_servicios');
            $table->enum('tipo_valor', ['porcentaje', 'monto_fijo']);
            $table->decimal('valor', 10, 2); // % o monto según tipo_valor
            $table->text('descripcion')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('oferta_forma_pago');
    }
};
