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
        Schema::create('contrato_forma_pago', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contrato_servicio_id')->constrained('contratos_servicios')->cascadeOnDelete();
            $table->unsignedTinyInteger('orden')->default(0);
            $table->enum('tipo', ['anticipo', 'contra_servicio', 'saldo_final']);
            $table->foreignId('catalogo_servicio_id')->nullable()->constrained('catalogo_servicios');
            $table->enum('tipo_valor', ['porcentaje', 'monto_fijo']);
            $table->decimal('valor', 10, 2);
            $table->text('descripcion')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contrato_forma_pago');
    }
};
