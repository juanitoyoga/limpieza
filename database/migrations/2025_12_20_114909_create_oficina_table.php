<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('oficinas', function (Blueprint $table) {
            $table->id();

            // Información institucional
            $table->string('code', 20)->unique(); // Ej: DMQ-AMB-OBR
            $table->string('name');
            $table->string('short_name')->nullable();
            $table->text('description')->nullable();

            // Información administrativa
            $table->string('address')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();

            // Estado informativo
            $table->boolean('is_active')->default(true);

            $table->timestamps();
            $table->softDeletes();

            $table->foreignId('oficina_id')
            ->constrained('funcionarios')
            ->cascadeOnUpdate()
            ->restrictOnDelete();

            $table->foreignId('oficina_id')
            ->constrained('auditores');
      
            $table->foreignId('oficina_id')
                ->constrained('supervisores');

      
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('oficinas');
    }
};
