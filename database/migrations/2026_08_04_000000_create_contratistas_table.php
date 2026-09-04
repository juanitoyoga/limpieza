<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Vincula un User con un Proveedor, representando a la persona que
     * opera la app móvil en campo para ese proveedor. Un proveedor puede
     * tener varios contratistas (cuadrillas/trabajadores distintos);
     * un mismo user no puede repetirse como contratista activo del
     * mismo proveedor.
     */
    public function up(): void
    {
        Schema::create('contratistas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('proveedor_id')->constrained('proveedores');
            $table->foreignId('user_id')->constrained('users');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['proveedor_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contratistas');
    }
};
