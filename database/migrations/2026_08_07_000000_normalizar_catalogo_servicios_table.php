<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Reemplaza los campos string libres (tipo, subtipo, ambito, frecuencia,
     * nivel_intervencion) por FKs a las tablas normalizadas nuevas
     * (ServiceType, ServiceSubtype, ServiceScope, Frequency,
     * InterventionLevel). El emparejamiento de datos viejos se hace por
     * columna 'code' — AJUSTAR si tus seeders usaron otro criterio.
     *
     * 'equipamiento' se ELIMINA por completo: Equipment ahora se relaciona
     * con ServiceSubtype (no con CatalogoServicios), así que el equipo
     * de un servicio del catálogo se deriva de su subtipo, no se guarda
     * en esta tabla.
     */
    public function up(): void
    {
        Schema::table('catalogo_servicios', function (Blueprint $table) {
            if (! Schema::hasColumn('catalogo_servicios', 'service_type_id')) {
                $table->foreignId('service_type_id')->nullable()->after('tipo')
                    ->constrained('service_types');
            }
            if (! Schema::hasColumn('catalogo_servicios', 'service_subtype_id')) {
                $table->foreignId('service_subtype_id')->nullable()->after('service_type_id')
                    ->constrained('service_subtypes');
            }
            if (! Schema::hasColumn('catalogo_servicios', 'service_scope_id')) {
                $table->foreignId('service_scope_id')->nullable()->after('service_subtype_id')
                    ->constrained('service_scopes');
            }
            if (! Schema::hasColumn('catalogo_servicios', 'frequency_id')) {
                $table->foreignId('frequency_id')->nullable()->after('service_scope_id')
                    ->constrained('frequencies');
            }
            if (! Schema::hasColumn('catalogo_servicios', 'intervention_level_id')) {
                $table->foreignId('intervention_level_id')->nullable()->after('frequency_id')
                    ->constrained('intervention_levels');
            }
        });

        // Migración de datos: empareja por 'code'. Cualquier valor viejo
        // que no encuentre match queda con FK null — se reporta al final
        // para revisión manual en vez de fallar silenciosamente.
        $sinMatch = [];

        if (Schema::hasColumn('catalogo_servicios', 'tipo')) {
            DB::table('catalogo_servicios')->whereNotNull('tipo')->orderBy('id')
                ->chunkById(200, function ($rows) use (&$sinMatch) {
                    foreach ($rows as $row) {
                        $tipoId = DB::table('service_types')->where('code', $row->tipo)->value('id');
                        $subtipoId = $row->subtipo
                            ? DB::table('service_subtypes')->where('code', $row->subtipo)->value('id')
                            : null;
                        $ambitoId = $row->ambito
                            ? DB::table('service_scopes')->where('code', $row->ambito)->value('id')
                            : null;
                        $frecuenciaId = $row->frecuencia
                            ? DB::table('frequencies')->where('code', $row->frecuencia)->value('id')
                            : null;
                        $nivelId = $row->nivel_intervencion
                            ? DB::table('intervention_levels')->where('code', $row->nivel_intervencion)->value('id')
                            : null;

                        DB::table('catalogo_servicios')->where('id', $row->id)->update([
                            'service_type_id' => $tipoId,
                            'service_subtype_id' => $subtipoId,
                            'service_scope_id' => $ambitoId,
                            'frequency_id' => $frecuenciaId,
                            'intervention_level_id' => $nivelId,
                        ]);

                        if (! $tipoId) {
                            $sinMatch[] = "id={$row->id} tipo='{$row->tipo}' sin match en service_types.code";
                        }
                    }
                });
        }

        if (! empty($sinMatch)) {
            \Illuminate\Support\Facades\Log::warning(
                '[normalizar_catalogo_servicios] Registros sin match de tipo tras la migración: ' . implode('; ', $sinMatch)
            );
        }

        // Limpieza de columnas obsoletas
        Schema::table('catalogo_servicios', function (Blueprint $table) {
            foreach (['tipo', 'subtipo', 'ambito', 'frecuencia', 'nivel_intervencion', 'equipamiento'] as $col) {
                if (Schema::hasColumn('catalogo_servicios', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }

    public function down(): void
    {
        // No reversible de forma segura dada la transformación de datos.
    }
};
