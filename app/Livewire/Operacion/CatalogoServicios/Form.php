<?php

namespace App\Livewire\Operacion\CatalogoServicios;

use App\Models\CatalogoServicios;
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
    public $tipo;
    public $subtipo;
    public $ambito;
    public $frecuencia;
    public $nivel_intervencion;
    public $equipamiento;
    public $unidad_medida;
    public $costo_referencial;
    public $orden = 0;
    public $estado = true;

    public $tipos = [
        'limpieza_viaria',
        'mantenimiento_parques',
        'limpieza_edificios',
        'eliminacion_grafitis',
        'higiene_canina',
        'mantenimiento_vial',
        'mantenimiento_fuentes',
        'gestion_residuos',
        'limpieza_post_eventos',
        'control_vegetacion',
    ];

    public $subtipos = [
        'barrido_manual',
        'barrido_mecanico',
        'baldeo',
        'hidrolavado',
        'jardineria',
        'poda',
        'desbroce',
        'bacheo',
        'pintura_vial',
        'limpieza_quimica',
        'limpieza_proyeccion',
        'retiro_escombros',
        'sanitizacion',
        'limpieza_profunda',
    ];

    public $ambitos = [
        'calle',
        'avenida',
        'parque',
        'iglesia',
        'cancha',
        'edificio_barrial',
        'mercado',
        'terminal',
        'zona_turistica',
        'monumento',
    ];

    public $frecuencias = ['diaria', 'semanal', 'mensual', 'bajo_demanda'];
    public $niveles = ['basico', 'medio', 'integral'];
    public $equipos = [
        'cuadrilla_manual',
        'barredora',
        'hidrolavadora',
        'camiones_recolectores',
        'desbrozadora',
        'productos_quimicos',
    ];
    public $unidades = ['m2', 'ml', 'hora', 'unidad', 'kg'];

    public function mount($id = null)
    {
        if ($id) {
            $this->item = CatalogoServicios::findOrFail($id);
            $this->fill($this->item->only([
                'codigo',
                'nombre',
                'descripcion',
                'tipo',
                'subtipo',
                'ambito',
                'frecuencia',
                'nivel_intervencion',
                'equipamiento',
                'unidad_medida',
                'costo_referencial',
                'orden',
                'estado',
            ]));
        }
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

            // Largos alineados con la migración (índice único compuesto)
            'tipo' => [
                'required',
                'string',
                'max:100',
                // Valida la combinación completa, no solo "tipo" aislado
                Rule::unique('catalogo_servicios')
                    ->where(fn($query) => $query
                        ->where('subtipo', $this->subtipo)
                        ->where('ambito', $this->ambito)
                        ->where('nivel_intervencion', $this->nivel_intervencion))
                    ->ignore($this->item?->id),
            ],
            'subtipo' => 'nullable|string|max:100',
            'ambito' => 'nullable|string|max:100',
            'frecuencia' => 'nullable|string|max:255',
            'nivel_intervencion' => 'nullable|string|max:50',
            'equipamiento' => 'nullable|string|max:255',
            'unidad_medida' => 'nullable|string|max:255',
            'costo_referencial' => 'nullable|numeric|min:0|max:99999999.99',
            'orden' => 'nullable|integer|min:0',
            'estado' => 'boolean',
        ];
    }

    protected function messages(): array
    {
        return [
            'tipo.unique' => 'Ya existe un servicio con esta misma combinación de tipo, subtipo, ámbito y nivel de intervención.',
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
        return view('livewire.operacion.catalogo-servicios.form');
    }
}
