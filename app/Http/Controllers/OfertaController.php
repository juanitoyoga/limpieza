<?php

namespace App\Http\Controllers;

use App\Models\CatalogoServicios;
use App\Models\Oferta;
use App\Models\OfertaServicio;
use App\Models\Proveedor;
use App\Models\Resolucion;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class OfertaController extends Controller
{
    /**
     * Muestra el listado de ofertas con sus relaciones principales.
     */
    public function index(Request $request): View
    {
        $query = Oferta::with(['resolucion', 'proveedor'])
            ->withCount('ofertaServicios');

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        if ($request->filled('buscar')) {
            $buscar = $request->buscar;
            $query->where(function ($q) use ($buscar) {
                $q->where('codigo', 'like', "%{$buscar}%")
                    ->orWhereHas('proveedor', fn($p) => $p->where('razon_social', 'like', "%{$buscar}%"));
            });
        }

        $ofertas = $query->latest()->paginate(10)->withQueryString();

        return view('livewire.operacion.ofertas.index', compact('ofertas'));
    }

    /**
     * Muestra el formulario para crear una nueva oferta.
     */
    public function create(): View
    {
        $resoluciones = Resolucion::with('resolucionServicios.catalogoServicio')->get();
        $proveedores = Proveedor::activos()->get();
        $catalogoServicios = CatalogoServicios::activos()->get();

        return view('livewire.operacion.ofertas.create', compact('resoluciones', 'proveedores', 'catalogoServicios'));
    }

    /**
     * Guarda la oferta y sus servicios detallados.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate($this->reglasBase());

        try {
            DB::transaction(function () use ($validated) {
                // 1. Crear la oferta principal
                $oferta = Oferta::create([
                    'resolucion_id' => $validated['resolucion_id'],
                    'proveedor_id' => $validated['proveedor_id'],
                    'codigo' => $validated['codigo'] ?? null,
                    'estado' => $validated['estado'],
                    'observaciones' => $validated['observaciones'] ?? null,
                    'monto_total' => 0, // Se actualiza vía evento del modelo al crear los items
                ]);

                // 2. Crear las líneas de servicio (los eventos del modelo actualizan subtotal y monto_total)
                foreach ($validated['servicios'] as $item) {
                    $oferta->ofertaServicios()->create($item);
                }
            });
        } catch (QueryException $e) {
            return back()->withInput()
                ->with('error', 'No se pudo guardar la oferta: revisa que no haya servicios duplicados en la lista.');
        }

        return redirect()->route('livewire.operacion.ofertas.index')
            ->with('success', 'Oferta registrada correctamente.');
    }

    /**
     * Muestra los detalles de una oferta específica.
     */
    public function show(Oferta $oferta): View
    {
        $oferta->load([
            'resolucion',
            'proveedor',
            'ofertaServicios.catalogoServicio',
            'ofertaServicios.resolucionServicio',
        ]);

        return view('ofertas.show', compact('oferta'));
    }

    /**
     * Muestra el formulario para editar una oferta existente.
     */
    public function edit(Oferta $oferta): View
    {
        $oferta->load('ofertaServicios');
        $resoluciones = Resolucion::with('resolucionServicios.catalogoServicio')->get();
        $proveedores = Proveedor::activos()->get();
        $catalogoServicios = CatalogoServicios::activos()->get();

        return view('livewire.operacion.ofertas.edit', compact('oferta', 'resoluciones', 'proveedores', 'catalogoServicios'));
    }

    /**
     * Actualiza la oferta y sus líneas de servicio.
     */
    public function update(Request $request, Oferta $oferta): RedirectResponse
    {
        $reglas = $this->reglasBase($oferta);

        // El id de cada línea debe pertenecer a ESTA oferta, no a cualquier fila de la tabla
        // (evita que alguien manipule el formulario y reasigne/edite líneas de otra oferta).
        $reglas['servicios.*.id'] = [
            'nullable',
            Rule::exists('oferta_servicios', 'id')->where('oferta_id', $oferta->id),
        ];

        $validated = $request->validate($reglas);

        try {
            DB::transaction(function () use ($validated, $oferta) {
                $oferta->update([
                    'resolucion_id' => $validated['resolucion_id'],
                    'proveedor_id' => $validated['proveedor_id'],
                    'codigo' => $validated['codigo'] ?? null,
                    'estado' => $validated['estado'],
                    'observaciones' => $validated['observaciones'] ?? null,
                ]);

                // Sincronización de ítems: eliminar los que ya no vienen en el array
                $idsEnviados = collect($validated['servicios'])->pluck('id')->filter()->toArray();
                $oferta->ofertaServicios()->whereNotIn('id', $idsEnviados)->delete();

                // Insertar / actualizar los items recibidos
                foreach ($validated['servicios'] as $item) {
                    if (! empty($item['id'])) {
                        $oferta->ofertaServicios()
                            ->whereKey($item['id'])
                            ->first()
                            ?->update($item);
                    } else {
                        $oferta->ofertaServicios()->create($item);
                    }
                }

                // El recálculo también ocurre por evento, pero lo forzamos por si
                // hubo eliminaciones (whereNotIn->delete no dispara el evento "deleted").
                $oferta->recalcularMontoTotal();
            });
        } catch (QueryException $e) {
            return back()->withInput()
                ->with('error', 'No se pudo actualizar la oferta: revisa que no haya servicios duplicados en la lista.');
        }

        return redirect()->route('livewire.operacion.ofertas.index')
            ->with('success', 'Oferta actualizada exitosamente.');
    }

    /**
     * Elimina suavemente (SoftDelete) una oferta.
     */
    public function destroy(Oferta $oferta): RedirectResponse
    {
        $oferta->delete();

        return redirect()->route('livewire.operacion.ofertas.index')
            ->with('success', 'Oferta eliminada correctamente.');
    }

    /**
     * Reglas de validación compartidas entre store() y update().
     */
    private function reglasBase(?Oferta $oferta = null): array
    {
        return [
            'resolucion_id' => 'required|exists:resoluciones,id',
            'proveedor_id' => 'required|exists:proveedores,id',
            'codigo' => [
                'nullable',
                'string',
                'max:50',
                Rule::unique('ofertas', 'codigo')->ignore($oferta?->id),
            ],
            'estado' => 'required|in:' . implode(',', Oferta::ESTADOS),
            'observaciones' => 'nullable|string',
            'servicios' => 'required|array|min:1',
            'servicios.*.catalogo_servicio_id' => 'required|distinct|exists:catalogo_servicios,id',
            'servicios.*.resolucion_servicio_id' => 'nullable|exists:resolucion_servicios,id',
            'servicios.*.cantidad' => 'required|integer|min:1',
            'servicios.*.costo_unitario' => 'required|numeric|min:0',
            'servicios.*.observaciones' => 'nullable|string',
        ];
    }
}
