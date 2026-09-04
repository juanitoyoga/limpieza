<?php

namespace Database\Seeders;

use App\Models\CatalogoServicios;
use App\Models\Frequency;
use App\Models\InterventionLevel;
use App\Models\ServiceScope;
use App\Models\ServiceSubtype;
use App\Models\ServiceType;
use App\Models\Unit;
use Illuminate\Database\Seeder;

class CatalogoServiciosSeeder extends Seeder
{
    private const OBJETIVO = 100;

    public function run(): void
    {
        $tipos = ServiceType::where('active', true)->with('subtypes')->get();
        $ambitos = ServiceScope::where('active', true)->get();
        $frecuencias = Frequency::where('active', true)->get();
        $niveles = InterventionLevel::where('active', true)->get();
        $unidades = Unit::where('active', true)->get();

        if (
            $tipos->isEmpty() || $ambitos->isEmpty() || $frecuencias->isEmpty()
            || $niveles->isEmpty() || $unidades->isEmpty()
        ) {
            $this->command->warn(
                'CatalogoServiciosSeeder: alguna tabla de referencia está vacía '
                    . '(ServiceType/ServiceScope/Frequency/InterventionLevel/Unit). '
                    . 'Corre esos seeders primero — no se generó nada.'
            );
            return;
        }

        // Combos ya existentes en BD, para no violar el unique
        // (service_type_id, service_subtype_id, service_scope_id, intervention_level_id)
        // ni al reintentar correr el seeder dos veces.
        $combosExistentes = CatalogoServicios::query()
            ->get(['service_type_id', 'service_subtype_id', 'service_scope_id', 'intervention_level_id'])
            ->map(fn($c) => $this->claveCombo(
                $c->service_type_id,
                $c->service_subtype_id,
                $c->service_scope_id,
                $c->intervention_level_id
            ))
            ->flip();

        // Todas las combinaciones tipo→subtipo posibles (subtipo puede ser
        // null si el tipo no tiene subtipos cargados todavía).
        $paresTipoSubtipo = $tipos->flatMap(function (ServiceType $tipo) {
            if ($tipo->subtypes->isEmpty()) {
                return [[$tipo, null]];
            }
            return $tipo->subtypes->map(fn(ServiceSubtype $subtipo) => [$tipo, $subtipo]);
        })->shuffle();

        $creados = 0;
        $intentosMax = self::OBJETIVO * 20; // margen generoso para combos ya usados
        $intentos = 0;

        while ($creados < self::OBJETIVO && $intentos < $intentosMax) {
            $intentos++;

            [$tipo, $subtipo] = $paresTipoSubtipo[$intentos % $paresTipoSubtipo->count()];
            $ambito = $ambitos->random();
            $frecuencia = $frecuencias->random();
            $nivel = $niveles->random();
            $unidad = $unidades->random();

            $clave = $this->claveCombo($tipo->id, $subtipo?->id, $ambito->id, $nivel->id);

            if (isset($combosExistentes[$clave])) {
                continue; // combo ya usado, prueba otra combinación en la siguiente vuelta
            }

            CatalogoServicios::create([
                'nombre' => $this->generarNombre($tipo, $subtipo, $ambito),
                'descripcion' => "Servicio de {$tipo->name}"
                    . ($subtipo ? " ({$subtipo->name})" : '')
                    . " en {$ambito->name}.",
                'service_type_id' => $tipo->id,
                'service_subtype_id' => $subtipo?->id,
                'service_scope_id' => $ambito->id,
                'frequency_id' => $frecuencia->id,
                'intervention_level_id' => $nivel->id,
                'unit_id' => $unidad->id,
                'costo_referencial' => fake()->randomFloat(2, 5, 500),
                'orden' => $creados,
                'estado' => true,
            ]);

            $combosExistentes[$clave] = true;
            $creados++;
        }

        if ($creados < self::OBJETIVO) {
            $this->command->warn(
                "CatalogoServiciosSeeder: solo se pudieron generar {$creados}/" . self::OBJETIVO
                    . ' registros — se agotaron las combinaciones únicas posibles con los datos de referencia actuales.'
            );
        } else {
            $this->command->info("CatalogoServiciosSeeder: {$creados} registros creados.");
        }
    }

    private function claveCombo(?int $tipoId, ?int $subtipoId, ?int $ambitoId, ?int $nivelId): string
    {
        return implode('-', [$tipoId, $subtipoId, $ambitoId, $nivelId]);
    }

    private function generarNombre(ServiceType $tipo, ?ServiceSubtype $subtipo, ServiceScope $ambito): string
    {
        $partes = array_filter([$tipo->name, $subtipo?->name, $ambito->name]);
        return implode(' - ', $partes);
    }
}
