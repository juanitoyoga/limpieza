<?php

namespace App\Http\Controllers;

use App\Models\Barrio;
use App\Models\BarrioAtributo;
use App\Models\Ordenanza332;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class BarrioAtributoController extends Controller
{
    public function index()
    {
        $barrios = Barrio::orderBy('nombre')->get(['id', 'nombre']);
        $ordenanzas = Ordenanza332::orderBy('descripcion')->get(['id', 'descripcion']);

        return view('livewire.admin.barriosAtributos.index', compact('barrios', 'ordenanzas'));
    }

    public function create()
    {
        $barrios = Barrio::orderBy('nombre')->get(['id', 'nombre']);
        $ordenanzas = Ordenanza332::orderBy('descripcion')->get(['id', 'descripcion']);

        return view('livewire.admin.barriosAtributos.create', compact('barrios', 'ordenanzas'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'barrio_id'       => ['required', 'integer', Rule::exists(Barrio::class, 'id')],
            'ordenanza332_id' => ['required', 'integer', Rule::exists(Ordenanza332::class, 'id')],
            'plazo_horas'     => 'nullable|integer|min:1',
            'nro_convenio'    => 'nullable|string|max:255',
        ]);

        $existe = BarrioAtributo::where('barrio_id', $validated['barrio_id'])
            ->where('ordenanza332_id', $validated['ordenanza332_id'])
            ->exists();

        if ($existe) {
            return back()->withInput()->withErrors([
                'barrio_id' => 'Ya existe un registro para esta combinación de barrio y contravención.',
            ]);
        }

        BarrioAtributo::create($validated);

        return redirect()
            ->route('barrio-atributo.lista')
            ->with('message', 'Registro creado correctamente.');
    }

    public function show($id)
    {
        // Cargamos el registro junto con sus relaciones con "barrio" y "ordenanza"
        // (Asegúrate de que estos nombres coincidan con los métodos de relación de tu modelo BarrioAtributo)
        $barrioAtributo = BarrioAtributo::with(['barrio', 'ordenanza'])->findOrFail($id);

        return view('livewire.admin.barriosAtributos.show', compact('barrioAtributo'));
    }

    public function edit(BarrioAtributo $barrioAtributo)
    {
        $barrios = Barrio::orderBy('nombre')->get(['id', 'nombre']);
        $ordenanzas = Ordenanza332::orderBy('descripcion')->get(['id', 'descripcion']);

        return view('livewire.admin.barriosAtributos.edit', compact('barrioAtributo', 'barrios', 'ordenanzas'));
    }

    public function update(Request $request, BarrioAtributo $barrioAtributo)
    {
        $validated = $request->validate([
            'barrio_id'       => ['required', 'integer', Rule::exists(Barrio::class, 'id')],
            'ordenanza332_id' => ['required', 'integer', Rule::exists(Ordenanza332::class, 'id')],
            'plazo_horas'     => 'nullable|integer|min:1',
            'nro_convenio'    => 'nullable|string',
        ]);

        $existe = BarrioAtributo::where('barrio_id', $validated['barrio_id'])
            ->where('ordenanza332_id', $validated['ordenanza332_id'])
            ->where('id', '!=', $barrioAtributo->id)
            ->exists();

        if ($existe) {
            return back()->withInput()->withErrors([
                'barrio_id' => 'Ya existe otro registro para esta combinación de barrio y contravención.',
            ]);
        }

        $barrioAtributo->update($validated);

        return redirect()
            ->route('barrio-atributo.lista')
            ->with('message', 'Registro actualizado correctamente.');
    }
}
