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
        Schema::create('service_subtype_equipment', function (Blueprint $table) {
            $table->id();

            $table->foreignId('service_subtype_id')
                ->constrained('service_subtypes')
                ->cascadeOnDelete();

            $table->foreignId('equipment_id')
                ->constrained('equipment')
                ->restrictOnDelete();

            $table->decimal('quantity', 10, 2)
                ->default(1)
                ->comment('Cantidad necesaria del equipo o material');

            $table->boolean('required')
                ->default(true)
                ->comment('Indica si el equipo es obligatorio');

            $table->text('notes')
                ->nullable()
                ->comment('Observaciones de uso');

            $table->timestamps();

            $table->unique([
                'service_subtype_id',
                'equipment_id',
            ]);
        });
    }

    /**
     * Revertir la migración.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_subtype_equipment');
    }
};
