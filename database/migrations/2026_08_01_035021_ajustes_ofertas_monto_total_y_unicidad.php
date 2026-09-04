<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {


        Schema::table('ofertas', function (Blueprint $table) {
            $table->unique(['proveedor_id', 'resolucion_id'], 'ofertas_proveedor_resolucion_unique');
        });
    }

    public function down(): void
    {
        Schema::table('ofertas', function (Blueprint $table) {
            $table->dropUnique('ofertas_proveedor_resolucion_unique');
        });
    }
};
