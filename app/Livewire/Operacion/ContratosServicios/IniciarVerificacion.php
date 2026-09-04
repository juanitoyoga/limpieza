<?php

namespace App\Livewire\Operacion\ContratosServicios;

use App\Models\{ContratoServicioDetalle, EvidenciaHito, HitoContratoServicio};

use Illuminate\Support\Facades\{DB, Gate};

use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Component;

/**
 * El Dirigente ve aquí los servicios con "ejecución completa" (ANTES y
 * DESPUES ya capturados por el contratista) que todavía NO tienen Hito —
 * y decide cuáles empezar a verificar. Este es el ÚNICO lugar donde nace
 * HitoContratoServicio: no se crea automáticamente al sincronizar
 * evidencia desde el móvil, ni tampoco arranca el blockchain aquí — eso
 * sigue pasando recién cuando alguien llama a verificar()/aprobar()
 * (HitoContratoServicioObserver ya se encarga de eso).
 */
class IniciarVerificacion  extends Component
{
    public function mount(): void
    {
        Gate::authorize('iniciarverificacion');
    }

    #[Computed]
    public function detalles()
    {
        $barrioResponsable = auth()->user()->barrioComoResponsable();

        if ($barrioResponsable === null) {
            return collect();
        }

        return ContratoServicioDetalle::query()
            ->with([
                'catalogoServicio',
                'contratoServicio.resolucion',
                'evidenciasHito.capturadoPor',
            ])
            ->whereHas(
                'contratoServicio.resolucion',
                fn($q) => $q->where('barrio_id', $barrioResponsable)
            )
            ->get()
            ->filter(fn(ContratoServicioDetalle $d) => $d->pendienteDeIniciarVerificacion())
            ->values();
    }

    public function iniciarVerificacion(int $detalleId): void
    {
        $detalle = ContratoServicioDetalle::with(['evidenciasHito', 'contratoServicio.resolucion'])
            ->findOrFail($detalleId);

        Gate::authorize('iniciarverificacion', $detalle);

        abort_unless(
            $detalle->pendienteDeIniciarVerificacion(),
            422,
            'Este servicio no está listo para iniciar verificación (falta evidencia, o ya tiene Hito).'
        );

        $antes   = $detalle->evidenciaAntes();
        $despues = $detalle->evidenciaDespues();

        DB::transaction(function () use ($detalle, $antes, $despues) {
            $hito = HitoContratoServicio::create([
                'uuid'                          => (string) Str::uuid(),
                'contratos_servicios_id'        => $detalle->contrato_servicio_id,
                'contrato_servicio_detalle_id'  => $detalle->id,
                // El "autor" del hito es el contratista que ejecutó el
                // trabajo (registró el ANTES), no el Dirigente que
                // inicia la verificación.
                'user_id'                       => $antes->user_id,
                'capturado_en_campo_at'         => $antes->capturado_en_campo_at,
            ]);

            EvidenciaHito::whereIn('id', [$antes->id, $despues->id])
                ->update(['hitos_contrato_servicio_id' => $hito->id]);
        });

        unset($this->detalles);

        session()->flash('success', "Verificación iniciada para el servicio #{$detalle->id}.");
    }

    public function render()
    {
        return view('livewire.operacion.contratos-servicios.iniciarverificacion');
    }
}
