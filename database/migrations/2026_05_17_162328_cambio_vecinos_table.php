<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Convierte ocupacion, deportes y recreacion de string a JSON.
     * Los valores de texto plano existentes se envuelven en array.
     */
    public function up(): void
    {
        // 1. Convertir datos existentes a JSON válido antes de cambiar el tipo
        foreach (['ocupacion', 'deportes', 'recreacion'] as $campo) {
            DB::table('vecinos')
                ->whereNotNull($campo)
                ->orderBy('id')
                ->get(['id', $campo])
                ->each(function ($row) use ($campo) {
                    $valor = $row->$campo;
                    // Si NO es JSON válido, convertir texto plano a array JSON
                    json_decode($valor);
                    if (json_last_error() !== JSON_ERROR_NONE) {
                        DB::table('vecinos')
                            ->where('id', $row->id)
                            ->update([$campo => json_encode([$valor])]);
                    }
                });
        }

        // 2. Cambiar el tipo de columna a JSON
        Schema::table('vecinos', function (Blueprint $table) {
            $table->json('ocupacion')->nullable()->change();
            $table->json('deportes')->nullable()->change();
            $table->json('recreacion')->nullable()->change();
        });
    }

    /**
     * Revertir a string si se hace rollback.
     */
    public function down(): void
    {
        Schema::table('vecinos', function (Blueprint $table) {
            $table->string('ocupacion')->nullable()->change();
            $table->string('deportes')->nullable()->change();
            $table->string('recreacion')->nullable()->change();
        });
    }
};
