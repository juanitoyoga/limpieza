<?php

namespace App\Livewire\Operacion\ContratosServicios;

use App\Livewire\Concerns\ManejaEstadoBloqueado;
use App\Models\{ContratoServicio, ContratoFormaPago, OrdenPago, HitoContratoServicio, AuditEvent};
use App\Jobs\RegistrarEventoBlockchain;
use Illuminate\Support\Facades\{Auth, DB, Gate, Log};
use Livewire\Attributes\{Layout, Computed};
use Livewire\Component;

#[Layout('layouts.operacion')]
class GestionOrdenesPago extends Component
{
    use ManejaEstadoBloqueado;

    public ContratoServicio $contrato;

    // --- Formulario de registro (Dirigente) ---
    public string $tipo = '';
    public ?int $contratoFormaPagoId = null;
    public array $hitosSeleccionados = [];
    public $monto = '';
    public string $observaciones = '';

    // --- Formularios de acciones puntuales sobre una orden existente ---
    public ?int $ordenSeleccionadaId = null;
    public string $referenciaPago = '';
    public string $motivoAnulacion = '';

    public function mount(ContratoServicio $contrato): void
    {
        $this->contrato = $contrato;

        if (Gate::denies('ordenes-pago.ver', $contrato)) {
            $this->bloquearAcceso(
                mensaje: 'No tienes permiso para ver las órdenes de pago de este contrato.',
                ruta: route('contratos-servicios.show', $contrato),
            );
            return;
        }

        if (
            $contrato->auth_status !== ContratoServicio::ESTADO_APROBADA
            && $contrato->auth_status !== ContratoServicio::ESTADO_LIQUIDADO
        ) {
            $this->bloquearAcceso(
                mensaje: 'El contrato debe estar Aprobado para gestionar órdenes de pago.',
                ruta: route('contratos-servicios.show', $contrato),
                detalles: ['Estado actual' => $contrato->estadoLabel()],
            );
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Datos computados — nunca modelos con relaciones como propiedad pública
    |--------------------------------------------------------------------------
    */

    #[Computed]
    public function planFormaPago()
    {
        return $this->contrato->formaPago()->with('catalogoServicio')->orderBy('orden')->get();
    }

    #[Computed]
    public function ordenesPago()
    {
        return OrdenPago::where('contrato_servicio_id', $this->contrato->id)
            ->with(['registrador', 'autorizador', 'pagador', 'anulador', 'hitos.detalle.catalogoServicio'])
            ->orderByDesc('id')
            ->get();
    }

    #[Computed]
    public function hitosDisponibles()
    {
        return OrdenPago::hitosDisponiblesParaOrden($this->contrato->id)
            ->load('detalle.catalogoServicio');
    }

    #[Computed]
    public function saldoRestante(): float
    {
        return round((float) $this->contrato->monto_total - OrdenPago::totalComprometido($this->contrato->id), 2);
    }

    #[Computed]
    public function existeAnticipo(): bool
    {
        return OrdenPago::yaExisteAnticipo($this->contrato->id);
    }

    /*
    |--------------------------------------------------------------------------
    | Registrar (Dirigente)
    |--------------------------------------------------------------------------
    */

    public function seleccionarLineaPlan(?int $contratoFormaPagoId): void
    {
        $this->contratoFormaPagoId = $contratoFormaPagoId;
        $this->hitosSeleccionados = [];
        $this->monto = '';

        if (! $contratoFormaPagoId) {
            $this->tipo = '';
            return;
        }

        $linea = $this->contrato->formaPago()->findOrFail($contratoFormaPagoId);
        $this->tipo = $linea->tipo;

        // Sugerencia de monto — el usuario puede ajustarlo antes de guardar
        $this->monto = $linea->montoEsperado($this->contrato);

        if ($linea->tipo === ContratoFormaPago::TIPO_CONTRA_SERVICIO) {
            // Preseleccionar los hitos aprobados de ese servicio, si los hay
            $this->hitosSeleccionados = $this->hitosDisponibles
                ->where('detalle.catalogo_servicio_id', $linea->catalogo_servicio_id)
                ->pluck('id')
                ->toArray();
        }
    }

    public function registrar(): void
    {
        if (Gate::denies('ordenes-pago.registrar', $this->contrato)) {
            $this->dispatch('toast', message: 'No tienes permiso para registrar órdenes de pago en este contrato.');
            return;
        }

        $this->validate([
            'tipo'   => 'required|in:anticipo,contra_servicio,saldo_final',
            'monto'  => 'required|numeric|min:0.01',
            'observaciones' => 'nullable|string|max:1000',
        ]);

        // Mapear tipo del plan a tipo de OrdenPago (mismo vocabulario salvo contra_servicio → hito)
        $tipoOrden = match ($this->tipo) {
            'contra_servicio' => OrdenPago::TIPO_HITO,
            default            => $this->tipo,
        };

        if ($tipoOrden === OrdenPago::TIPO_ANTICIPO && $this->existeAnticipo) {
            $this->dispatch('toast', message: 'Ya existe una orden de anticipo para este contrato.');
            return;
        }

        if ($tipoOrden === OrdenPago::TIPO_HITO && empty($this->hitosSeleccionados)) {
            $this->dispatch('toast', message: 'Debe seleccionar al menos un hito aprobado.');
            return;
        }

        if ((float) $this->monto > $this->saldoRestante) {
            $this->dispatch('toast', message: "El monto excede el saldo restante del contrato (\${$this->saldoRestante}).");
            return;
        }

        // Revalidar hitos server-side: siguen aprobados y libres (no confiar en el estado del componente)
        $hitosValidos = [];
        if ($tipoOrden === OrdenPago::TIPO_HITO) {
            $hitosValidos = HitoContratoServicio::whereIn('id', $this->hitosSeleccionados)
                ->whereHas('detalle', fn($q) => $q->where('contrato_servicio_id', $this->contrato->id))
                ->whereNotNull('aprobado_por')
                ->whereDoesntHave(
                    'ordenesPago',
                    fn($q) =>
                    $q->whereIn('estado', [OrdenPago::ESTADO_PENDIENTE, OrdenPago::ESTADO_AUTORIZADA])
                )
                ->pluck('id')
                ->toArray();

            if (count($hitosValidos) !== count($this->hitosSeleccionados)) {
                $this->dispatch('toast', message: 'Alguno de los hitos seleccionados ya no está disponible. Actualice la lista.');
                $this->hitosSeleccionados = [];
                return;
            }
        }

        try {
            $userId = Auth::id();

            DB::transaction(function () use ($userId, $tipoOrden, $hitosValidos) {
                $orden = OrdenPago::create([
                    'contrato_servicio_id'    => $this->contrato->id,
                    'contrato_forma_pago_id'  => $this->contratoFormaPagoId,
                    'tipo'                    => $tipoOrden,
                    'monto'                   => $this->monto,
                    'estado'                  => OrdenPago::ESTADO_PENDIENTE,
                    'registrado_por'          => $userId,
                    'fecha_registro'          => now(),
                    'observaciones'           => $this->observaciones,
                ]);

                if (! empty($hitosValidos)) {
                    $orden->hitos()->sync($hitosValidos);
                }

                $evento = AuditEvent::logEvent($orden, $userId, 'orden_pago_registrada', [
                    'contrato_servicio_id' => $this->contrato->id,
                    'tipo'   => $orden->tipo,
                    'monto'  => $orden->monto,
                    'hitos'  => $hitosValidos,
                ]);

                DB::afterCommit(fn() => RegistrarEventoBlockchain::dispatch($evento->id));
            });

            $this->reset(['tipo', 'contratoFormaPagoId', 'hitosSeleccionados', 'monto', 'observaciones']);
            $this->dispatch('toast', message: 'Orden de pago registrada correctamente.');
        } catch (\Throwable $e) {
            Log::error('[GestionOrdenesPago] Error al registrar', [
                'mensaje' => $e->getMessage(),
                'contrato_servicio_id' => $this->contrato->id,
            ]);
            $this->dispatch('toast', message: 'Error al registrar la orden de pago. Intente nuevamente.');
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Autorizar (Presidente)
    |--------------------------------------------------------------------------
    */

    public function autorizar(int $ordenId): void
    {
        if (Gate::denies('ordenes-pago.autorizar', $this->contrato)) {
            $this->dispatch('toast', message: 'No tienes permiso para autorizar órdenes de pago.');
            return;
        }

        $orden = OrdenPago::where('contrato_servicio_id', $this->contrato->id)->findOrFail($ordenId);

        if (! $orden->puedeAutorizarse()) {
            $this->dispatch('toast', message: 'Esta orden ya no está en estado Pendiente.');
            return;
        }

        // Revalidación server-side: los hitos asociados deben seguir aprobados
        if ($orden->tipo === OrdenPago::TIPO_HITO) {
            $todosAprobados = $orden->hitos()->whereNull('aprobado_por')->doesntExist();
            if (! $todosAprobados) {
                $this->dispatch('toast', message: 'Uno de los hitos asociados ya no está aprobado.');
                return;
            }
        }

        try {
            $userId = Auth::id();

            $orden->update([
                'estado'             => OrdenPago::ESTADO_AUTORIZADA,
                'autorizado_por'     => $userId,
                'fecha_autorizacion' => now(),
            ]);
            // AuditEvent + blockchain se disparan desde OrdenPagoObserver::updated()

            $this->dispatch('toast', message: 'Orden de pago autorizada.');
        } catch (\Throwable $e) {
            Log::error('[GestionOrdenesPago] Error al autorizar', [
                'mensaje' => $e->getMessage(),
                'orden_pago_id' => $ordenId,
            ]);
            $this->dispatch('toast', message: 'Error al autorizar la orden de pago.');
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Marcar pagada (Dirigente o Presidente)
    |--------------------------------------------------------------------------
    */

    public function abrirMarcarPagada(int $ordenId): void
    {
        $this->ordenSeleccionadaId = $ordenId;
        $this->referenciaPago = '';
    }

    public function confirmarPago(): void
    {
        if (Gate::denies('ordenes-pago.pagar', $this->contrato)) {
            $this->dispatch('toast', message: 'No tienes permiso para confirmar pagos en este contrato.');
            return;
        }

        $orden = OrdenPago::where('contrato_servicio_id', $this->contrato->id)
            ->findOrFail($this->ordenSeleccionadaId);

        if (! $orden->puedeMarcarsePagada()) {
            $this->dispatch('toast', message: 'La orden debe estar Autorizada antes de marcarse como Pagada.');
            return;
        }

        $this->validate(['referenciaPago' => 'nullable|string|max:255']);

        try {
            $userId = Auth::id();

            $orden->update([
                'estado'          => OrdenPago::ESTADO_PAGADA,
                'pagado_por'      => $userId,
                'fecha_pago'      => now(),
                'referencia_pago' => $this->referenciaPago ?: null,
            ]);

            $this->ordenSeleccionadaId = null;
            $this->referenciaPago = '';
            $this->dispatch('toast', message: 'Pago confirmado correctamente.');
        } catch (\Throwable $e) {
            Log::error('[GestionOrdenesPago] Error al confirmar pago', [
                'mensaje' => $e->getMessage(),
                'orden_pago_id' => $this->ordenSeleccionadaId,
            ]);
            $this->dispatch('toast', message: 'Error al confirmar el pago.');
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Anular
    |--------------------------------------------------------------------------
    */

    public function abrirAnular(int $ordenId): void
    {
        $this->ordenSeleccionadaId = $ordenId;
        $this->motivoAnulacion = '';
    }

    public function confirmarAnulacion(): void
    {
        if (Gate::denies('ordenes-pago.anular', $this->contrato)) {
            $this->dispatch('toast', message: 'No tienes permiso para anular órdenes de pago.');
            return;
        }

        $orden = OrdenPago::where('contrato_servicio_id', $this->contrato->id)
            ->findOrFail($this->ordenSeleccionadaId);

        if (! $orden->puedeAnularse()) {
            $this->dispatch('toast', message: 'Esta orden ya no puede anularse (está Pagada o ya Anulada).');
            return;
        }

        $user = Auth::user();

        // El Dirigente solo puede anular mientras siga Pendiente; una vez
        // Autorizada, solo el Presidente (o SuperAdmin) puede anularla.
        if (
            $orden->estado === OrdenPago::ESTADO_AUTORIZADA
            && ! in_array($user->role_name, ['Presidente', 'SuperAdmin'], true)
        ) {
            $this->dispatch('toast', message: 'Solo el Presidente puede anular una orden ya Autorizada.');
            return;
        }

        $this->validate(['motivoAnulacion' => 'required|string|min:5|max:1000']);

        try {
            $orden->update([
                'estado'           => OrdenPago::ESTADO_ANULADA,
                'anulado_por'      => $user->id,
                'fecha_anulacion'  => now(),
                'motivo_anulacion' => $this->motivoAnulacion,
            ]);

            $this->ordenSeleccionadaId = null;
            $this->motivoAnulacion = '';
            $this->dispatch('toast', message: 'Orden de pago anulada.');
        } catch (\Throwable $e) {
            Log::error('[GestionOrdenesPago] Error al anular', [
                'mensaje' => $e->getMessage(),
                'orden_pago_id' => $this->ordenSeleccionadaId,
            ]);
            $this->dispatch('toast', message: 'Error al anular la orden de pago.');
        }
    }

    public function render()
    {
        return $this->renderBloqueadoOr('livewire.operacion.contratos-servicios.gestionordenespago');
    }
}
