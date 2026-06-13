<?php

namespace App\Http\Controllers;

use App\Models\Barrio;
use Illuminate\Http\Request;

class BarrioController extends Controller
{
    /**
     * Listado de barrios
     */
    /**
     * Formulario de filtros — GET /barrios
     */
    public function index()
    {
        return view('livewire.admin.barrios.index');
    }

    /**
     * Activar/Desactivar — PATCH /barrios/{barrio}/toggle
     */
    public function toggle(Barrio $barrio)
    {
        $barrio->update(['activo' => !$barrio->activo]);

        return back()->with(
            'success',
            'Barrio ' . ($barrio->activo ? 'activado' : 'desactivado') . ' correctamente.'
        );
    }

    public function list()
    {
        $barrios = Barrio::orderBy('created_at', 'desc')->paginate(20);
        return view('livewire.admin.barrios.list', compact('barrios'));
    }

    /**
     * Formulario de creación
     */
    public function create()
    {
        return view('livewire.admin.barrios.create');
    }

    /**
     * Guardar nuevo barrio
     */
    public function store(Request $request)
    {
        $request->validate([
            'id_DMQ'    => 'required|string|max:50',
            'nombre'    => 'required|string|max:255',
            'sector'    => 'required|string|max:255',
            'parroquia' => 'required|string|max:255',
            'lat'       => 'required|numeric',
            'lng'       => 'required|numeric',
            'poligono'  => 'required|json',
        ]);

        Barrio::create([
            'id_DMQ'      => $request->id_DMQ,
            'nombre'      => $request->nombre,
            'sector'      => $request->sector,
            'parroquia'   => $request->parroquia,
            'coordenadas' => ['lat' => (float)$request->lat, 'lng' => (float)$request->lng],
            'polygon'     => json_decode($request->poligono, true),
        ]);

        return redirect()
            ->route('barrios.index')
            ->with('success', 'Barrio creado correctamente.');
    }

    public function update(Request $request, Barrio $barrio)
    {
        $request->validate([
            'id_DMQ'    => 'required|string|max:50',
            'nombre'    => 'required|string|max:255',
            'sector'    => 'required|string|max:255',
            'parroquia' => 'required|string|max:255',
            'lat'       => 'required|numeric',
            'lng'       => 'required|numeric',
            'poligono'  => 'required|json',
        ]);

        $barrio->update([
            'id_DMQ'      => $request->id_DMQ,
            'nombre'      => $request->nombre,
            'sector'      => $request->sector,
            'parroquia'   => $request->parroquia,
            'coordenadas' => ['lat' => (float)$request->lat, 'lng' => (float)$request->lng],
            'polygon'     => json_decode($request->poligono, true),
        ]);

        return redirect()
            ->route('barrios.index')
            ->with('success', 'Barrio actualizado correctamente.');
    }

    /**
     * Mostrar un barrio
     */
    public function show(Barrio $barrio)
    {
        return view('livewire.admin.barrios.show', compact('barrio'));
    }

    /**
     * Formulario de edición
     */
    public function edit(Barrio $barrio)
    {
        return view('barrios.edit', compact('barrio'));
    }


    /**
     * Eliminar barrio
     */
    public function destroy(Barrio $barrio)
    {
        $barrio->delete();

        return redirect()
            ->route('barrios.index')
            ->with('success', 'Barrio eliminado correctamente.');
    }
}
