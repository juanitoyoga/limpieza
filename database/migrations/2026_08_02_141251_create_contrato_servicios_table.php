<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contratos_servicios', function (Blueprint $table) {
            $table->id();

            // oferta_id corregido: apunta a 'ofertas', no a 'resoluciones'.
            // restrictOnDelete: este es un registro contractual/financiero,
            // no debe desaparecer en cascada si se borra la oferta/proveedor de origen.
            $table->foreignId('oferta_id')->constrained('ofertas')->restrictOnDelete();
            $table->foreignId('proveedor_id')->constrained('proveedores')->restrictOnDelete();

            $table->string('codigo')->unique();
            $table->string('titulo');
            $table->text('descripcion');

            $table->date('fecha_inicio');
            $table->date('fecha_fin_estimada')->nullable();

            $table->decimal('monto_total', 15, 2);

            // Documento + Blockchain (mismo patrón que resoluciones/ofertas)
            $table->string('documento_original_path', 1024)->nullable();
            $table->string('documento_original_hash', 128)->nullable();
            $table->string('documento_original_mime')->nullable();

            $table->json('evento_json')->nullable();

            $table->string('tx_hash', 128)->nullable();
            $table->unsignedBigInteger('tx_block')->nullable();
            $table->string('blockchain_contract_address', 128)->nullable();
            $table->unsignedBigInteger('blockchain_block_number')->nullable();
            $table->timestamp('blockchain_timestamp')->nullable();

            // Pendiente -> Verificada (Dirigente) -> Aprobada (Presidente)
            //           -> Rescindido | Liquidado (solo Presidente)
            // Rechazada disponible desde Pendiente/Verificada, igual que Resolucion/Oferta.
            $table->string('auth_status', 25)->default('Pendiente');

            // Mismo set de columnas de responsabilidad que Resolucion/Oferta,
            // más las dos nuevas para Rescindido y Liquidado (ambas solo Presidente).
            $table->foreignId('verificado_por')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('fecha_verificacion')->nullable();

            $table->foreignId('aprobado_por')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('fecha_aprobacion')->nullable();

            $table->foreignId('rechazado_por')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('fecha_rechazo')->nullable();

            $table->foreignId('rescindido_por')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('fecha_rescision')->nullable();

            $table->foreignId('liquidado_por')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('fecha_liquidacion')->nullable();

            $table->text('observaciones')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index('auth_status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contratos_servicios');
    }
};
