<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Un Contratista (empleado ya validado del proveedor) solo puede
     * capturar hitos en los contratos donde fue asignado explícitamente
     * — no en cualquier contrato aprobado de su proveedor. Esto separa
     * "es empleado de la empresa X" de "está trabajando en ESTE contrato".
     */
    public function up(): void
    {
        Schema::create('asignaciones_contrato_servicio', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contratista_id')->constrained('contratistas');
            $table->foreignId('contrato_servicio_id')->constrained('contratos_servicios');
            $table->foreignId('asignado_por')->constrained('users'); // quien hizo la asignación (Admin/Funcionario)
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(
                ['contratista_id', 'contrato_servicio_id'],
                'asignaciones_contratista_contrato_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asignaciones_contrato_servicio');
    }
};
