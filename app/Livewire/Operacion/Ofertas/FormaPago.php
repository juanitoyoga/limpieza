<?php

namespace App\Livewire\Operacion\Ofertas;

use Livewire\Component;
use App\Models\{Oferta, OfertaFormaPago};
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Livewire\Attributes\Layout;
use App\Livewire\Concerns\ManejaEstadoBloqueado;

#[Layout('layouts.operacion')]
class FormaPago extends Component
{
    use ManejaEstadoBloqueado;

    private const CAMPOS_EDITABLES = ['tipo_valor', 'valor', 'descripcion'];

    public Oferta $oferta;

    public $lineasFormaPago = [];
    public $serviciosOferta = []; // para poblar el select de catalogo_servicio_id
    public string $nuevoTipo = '';
    public ?int $nuevoCatalogoServicioId = null;
    public string $nuevoTipoValor = 'porcentaje';
    public $nuevoValor = '';
    public ?string $nuevaDescripcion = null;

    public function mount(Oferta $oferta)
    {
        $this->oferta = $oferta;



        if ($oferta->auth_status !== Oferta::ESTADO_PENDIENTE) {
            $this->bloquearAcceso(
                mensaje: 'No se puede modificar la forma de pago de una oferta ya verificada, aprobada o rechazada.',
                ruta: route('ofertas.show', $oferta),
                detalles: ['Estado actual' => $oferta->estadoLabel()],
            );
            return;
        }

        $this->actualizarListas();
    }

    private function verificarEditable(): void
    {
        if ($this->oferta->fresh()->auth_status !== Oferta::ESTADO_PENDIENTE) {
            $this->bloquearAcceso(
                mensaje: 'La oferta cambió de estado y ya no se puede editar.',
                ruta: route('ofertas.show', $this->oferta),
            );
        }
    }

    /*
|--------------------------------------------------------------------------
| Resumen financiero — usado en el panel lateral del blade
|--------------------------------------------------------------------------
*/

    public function totalOferta(): float
    {
        return (float) $this->oferta->monto_total;
    }

    public function totalAsignadoEnPlan(): float
    {
        return round(
            $this->lineasFormaPago->sum(fn($linea) => $linea->montoEsperado($this->oferta)),
            2
        );
    }

    /**
     * Incluye una previsualización de la línea que el usuario está armando
     * en el formulario (aún sin guardar), para que vea el impacto ANTES
     * de confirmar "Agregar línea".
     */
    public function totalAsignadoConPreview(): float
    {
        return round($this->totalAsignadoEnPlan() + $this->montoPreviewNuevaLinea(), 2);
    }

    public function montoPreviewNuevaLinea(): float
    {
        if (! $this->nuevoTipoValor || $this->nuevoValor === '' || $this->nuevoValor === null) {
            return 0.0;
        }

        if ($this->nuevoTipoValor === 'porcentaje') {
            $base = $this->nuevoTipo === 'contra_servicio' && $this->nuevoCatalogoServicioId
                ? (float) ($this->serviciosOferta->firstWhere('catalogo_servicio_id', $this->nuevoCatalogoServicioId)?->subtotal ?? 0)
                : $this->totalOferta();

            return round($base * ((float) $this->nuevoValor / 100), 2);
        }

        return round((float) $this->nuevoValor, 2);
    }

    public function saldoPendiente(): float
    {
        return round($this->totalOferta() - $this->totalAsignadoConPreview(), 2);
    }

    /**
     * A diferencia de los servicios (que se trasladan desde la resolución),
     * cada línea de forma de pago se ingresa manualmente. tipo y tipo_valor
     * llegan fijos según el botón que el usuario presiona en la vista
     * (ej. "Agregar anticipo", "Agregar pago contra servicio", "Agregar saldo final").
     */

    public function agregarLineaDesdeFormulario()
    {
        $this->agregarLinea(
            $this->nuevoTipo,
            $this->nuevoCatalogoServicioId ? (int) $this->nuevoCatalogoServicioId : null,
            $this->nuevoTipoValor,
            $this->nuevoValor,
            $this->nuevaDescripcion
        );

        // Limpiar formulario tras guardar
        $this->reset(['nuevoTipo', 'nuevoCatalogoServicioId', 'nuevoValor', 'nuevaDescripcion']);
    }

    public function agregarLinea(string $tipo, ?int $catalogoServicioId, string $tipoValor, $valor, ?string $descripcion = null)
    {
        $this->verificarEditable();

        if (! in_array($tipo, [OfertaFormaPago::TIPO_ANTICIPO, OfertaFormaPago::TIPO_CONTRA_SERVICIO, OfertaFormaPago::TIPO_SALDO_FINAL], true)) {
            $this->dispatch('toast', message: 'Tipo de línea inválido.');
            return;
        }

        if ($tipo === OfertaFormaPago::TIPO_CONTRA_SERVICIO) {
            if (! $catalogoServicioId) {
                $this->dispatch('toast', message: 'Debe seleccionar el servicio al que corresponde este pago.');
                return;
            }

            // El servicio debe pertenecer a esta oferta — no a cualquiera del catálogo
            if (! $this->oferta->ofertaServicios()->where('catalogo_servicio_id', $catalogoServicioId)->exists()) {
                $this->dispatch('toast', message: 'El servicio seleccionado no está en esta oferta.');
                return;
            }

            // Evitar dos líneas "contra_servicio" para el mismo servicio
            if ($this->oferta->formaPago()->where('catalogo_servicio_id', $catalogoServicioId)->exists()) {
                $this->dispatch('toast', message: 'Ya existe una línea de pago para ese servicio.');
                return;
            }
        } else {
            $catalogoServicioId = null;

            // Anticipo y saldo_final: solo uno de cada tipo por oferta
            if ($this->oferta->formaPago()->where('tipo', $tipo)->exists()) {
                $this->dispatch('toast', message: 'Ya existe una línea de tipo "' . $tipo . '" para esta oferta.');
                return;
            }
        }

        $validator = Validator::make(
            ['tipo_valor' => $tipoValor, 'valor' => $valor],
            [
                'tipo_valor' => 'required|in:porcentaje,monto_fijo',
                'valor'      => $tipoValor === 'porcentaje' ? 'required|numeric|min:0.01|max:100' : 'required|numeric|min:0.01',
            ]
        );

        if ($validator->fails()) {
            $this->dispatch('toast', message: $validator->errors()->first());
            return;
        }

        try {
            OfertaFormaPago::create([
                'oferta_id'            => $this->oferta->id,
                'orden'                => ($this->oferta->formaPago()->max('orden') ?? 0) + 1,
                'tipo'                 => $tipo,
                'catalogo_servicio_id' => $catalogoServicioId,
                'tipo_valor'           => $tipoValor,
                'valor'                => $valor,
                'descripcion'          => $descripcion,
            ]);
        } catch (\DomainException $e) {
            $this->dispatch('toast', message: $e->getMessage());
            return;
        }

        $this->actualizarListas();
    }

    public function actualizarLinea(int $id, string $campo, $valor)
    {
        $this->verificarEditable();

        abort_unless(in_array($campo, self::CAMPOS_EDITABLES, true), 403);

        $linea = OfertaFormaPago::where('oferta_id', $this->oferta->id)->findOrFail($id);

        if ($campo === 'valor') {
            $max = $linea->tipo_valor === 'porcentaje' ? '|max:100' : '';
            $validator = Validator::make(['valor' => $valor], ['valor' => "required|numeric|min:0.01{$max}"]);

            if ($validator->fails()) {
                $this->dispatch('toast', message: $validator->errors()->first('valor'));
                return;
            }
        }

        try {
            $linea->$campo = $valor;
            $linea->save();
        } catch (\DomainException $e) {
            $this->dispatch('toast', message: $e->getMessage());
            return;
        }

        $this->actualizarListas();
    }

    public function eliminarLinea(int $id)
    {
        $this->verificarEditable();

        OfertaFormaPago::where('oferta_id', $this->oferta->id)->findOrFail($id)->delete();
        $this->actualizarListas();
    }

    /**
     * Suma de líneas tipo "porcentaje" — informativo en la vista para
     * ayudar al usuario a cuadrar el plan en 100%. No bloquea el guardado
     * porque puede haber líneas monto_fijo mezcladas, que no cuentan aquí.
     */
    public function sumaPorcentajes(): float
    {
        return (float) $this->oferta->formaPago()
            ->where('tipo_valor', 'porcentaje')
            ->sum('valor');
    }

    private function actualizarListas()
    {
        $this->serviciosOferta = $this->oferta->ofertaServicios()
            ->with('catalogoServicio')
            ->get();

        $this->lineasFormaPago = $this->oferta->formaPago()
            ->with('catalogoServicio')
            ->orderBy('orden')
            ->get();
    }

    public function render()
    {
        return $this->renderBloqueadoOr('livewire.operacion.ofertas.formapago');
    }
}
