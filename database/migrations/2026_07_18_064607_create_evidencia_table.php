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
        Schema::create('evidencias', function (Blueprint $table) {
            $table->id();
            $table->morphs('evidenciable'); // evidenciable_type, evidenciable_id
            $table->enum('tipo', ['foto', 'video']);
            $table->string('disco')->default('local'); // abstracción de storage (config/filesystems.php)
            $table->string('ruta'); // path relativo dentro del disco
            $table->string('mime')->nullable();
            $table->unsignedBigInteger('tamano_bytes')->nullable();
            $table->unsignedInteger('duracion_segundos')->nullable(); // solo video
            $table->unsignedTinyInteger('orden')->default(0);
            $table->char('hash_archivo', 64); // sha256 calculado UNA vez al subir, nunca recalculado
            $table->timestamps();

            $table->index(['evidenciable_type', 'evidenciable_id', 'orden']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('evidencias');
    }
};
