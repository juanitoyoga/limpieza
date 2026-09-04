<?php

namespace App\Livewire\Operacion\CatalogoServicios;

use App\Models\{
    CatalogoServicios,
    Frequency,
    InterventionLevel,
    ServiceScope,
    ServiceSubtype,
    ServiceType,
    Unit,
};

use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.operacion')]
class Form extends Component
{
    public ?CatalogoServicios $item = null;

    public $codigo;
    public $nombre;
    public $descripcion;
    public $service_type_id;
    public $service_subtype_id;
    public $service_scope_id;
    public $frequency_id;
    public $intervention_level_id;
    public $unit_id;
    public $costo_referencial;
    public $orden = 0;
    public $estado = true;


    public function mount($id = null)
    {
        if ($id) {
            $this->item = CatalogoServicios::findOrFail($id);
            $this->fill($this->item->only([
                'codigo',
                'nombre',
                'descripcion',
                'service_type_id',
                'service_subtype_id',
                'service_scope_id',
                'frequency_id',
                'intervention_level_id',
                'unit_id',
                'costo_referencial',
                'orden',
                'estado',
            ]));
        }
    }

    /**
     * Al cambiar el tipo, el subtipo elegido anteriormente puede no
     * pertenecer al nuevo tipo — se limpia para evitar una combinación
     * inconsistente (ej. tipo=LIM con subtipo de otro tipo).
     */
    public function updatedServiceTypeId(): void
    {
        $this->service_subtype_id = null;
    }

    /**
     * Subtipos disponibles, ya filtrados por el tipo elegido. Vacío si
     * todavía no se eligió tipo — así el <select> de subtipo no muestra
     * el catálogo completo sin sentido.
     */
    public function getSubtiposDisponiblesProperty(): Collection
    {
        if (! $this->service_type_id) {
            return collect();
        }

        return ServiceSubtype::where('service_type_id', $this->service_type_id)
            ->where('active', true)
            ->orderBy('name')
            ->get();
    }

    /**
     * Equipo requerido por el subtipo elegido — informativo, ya NO es un
     * campo editable de este formulario (ver CatalogoServicios::equipoRequerido()).
     */
    public function getEquipoDelSubtipoProperty(): Collection
    {
        if (! $this->service_subtype_id) {
            return collect();
        }

        return ServiceSubtype::find($this->service_subtype_id)?->equipment ?? collect();
    }

    protected function rules(): array
    {
        return [
            'codigo' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('catalogo_servicios', 'codigo')->ignore($this->item?->id),
            ],
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',

            'service_type_id' => [
                'required',
                'exists:service_types,id',
                // Valida la combinación completa, no solo el tipo aislado
                Rule::unique('catalogo_servicios', 'service_type_id')
                    ->where(fn($query) => $query
                        ->where('service_subtype_id', $this->service_subtype_id)
                        ->where('service_scope_id', $this->service_scope_id)
                        ->where('intervention_level_id', $this->intervention_level_id))
                    ->ignore($this->item?->id),
            ],
            'service_subtype_id' => 'nullable|exists:service_subtypes,id',
            'service_scope_id' => 'nullable|exists:service_scopes,id',
            'frequency_id' => 'nullable|exists:frequencies,id',
            'intervention_level_id' => 'nullable|exists:intervention_levels,id',
            'unit_id' => 'nullable|exists:units,id',
            'costo_referencial' => 'nullable|numeric|min:0|max:99999999.99',
            'orden' => 'nullable|integer|min:0',
            'estado' => 'boolean',
        ];
    }

    protected function messages(): array
    {
        return [
            'service_type_id.unique' => 'Ya existe un servicio con esta misma combinación de tipo, subtipo, ámbito y nivel de intervención.',
            'codigo.unique' => 'Ya existe un servicio con ese código.',
        ];
    }

    public function save()
    {
        $data = $this->validate();

        if ($this->item) {
            $this->item->update($data);
            session()->flash('message', 'Servicio actualizado correctamente.');
        } else {
            CatalogoServicios::create($data);
            session()->flash('message', 'Servicio creado correctamente.');
        }

        return redirect()->route('catalogo-servicios.index');
    }

    public function render()
    {
        return view('livewire.operacion.catalogo-servicios.form', [
            'tiposDisponibles' => ServiceType::where('active', true)->orderBy('name')->get(),
            'ambitosDisponibles' => ServiceScope::where('active', true)->orderBy('name')->get(),
            'frecuenciasDisponibles' => Frequency::where('active', true)->orderBy('name')->get(),
            'nivelesDisponibles' => InterventionLevel::where('active', true)->orderBy('sort_order')->get(),
            'unidadesDisponibles' => Unit::where('active', true)->orderBy('name')->get(),
            'subtiposDisponibles' => $this->subtiposDisponibles,
            'equipoDelSubtipo' => $this->equipoDelSubtipo,
        ]);
    }
}
