<?php

namespace App\Livewire\Admin\Contratos;

use App\Models\Barrio;
use App\Models\Contrato;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Role;
use Livewire\Attributes\Layout;

#[Layout('layouts.admin')]
class Index extends Component
{
    use WithPagination;

    // ─── Filtros Avanzados ──────────────────────────────────────────────────────
    public ?int    $barrio_id             = null;
    public string  $estado                = '';
    public string  $numero_contrato       = '';
    public ?float  $monto_min             = null;
    public ?float  $monto_max             = null;
    public string  $fecha_inicio          = '';
    public string  $fecha_fin             = '';
    public string  $rol_ingreso           = '';
    public string  $rol_verificacion      = '';
    public string  $rol_aprobacion        = '';
    public string  $con_blockchain        = '';
    public string  $fecha_ingreso_inicio  = '';
    public string  $fecha_ingreso_fin     = '';

    // ─── Configuración de la Tabla ──────────────────────────────────────────────
    public int    $perPage        = 10;
    public string $sortField      = 'fecha_ingreso';
    public string $sortDirection  = 'desc';

    public function updated($property)
    {
        if (!in_array($property, ['sortField', 'sortDirection', 'perPage'])) {
            $this->resetPage();
        }
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function buscar(): void
    {
        $this->validate();
        $this->resetPage();
    }

    public function limpiar(): void
    {
        $this->reset([
            'barrio_id',
            'estado',
            'numero_contrato',
            'monto_min',
            'monto_max',
            'fecha_inicio',
            'fecha_fin',
            'rol_ingreso',
            'rol_verificacion',
            'rol_aprobacion',
            'con_blockchain',
            'fecha_ingreso_inicio',
            'fecha_ingreso_fin',
        ]);
        $this->resetPage();
    }

    /**
     * Redirección segura para edición
     * Evita que usuarios accedan alterando el HTML desde las herramientas de desarrollador.
     */
    public function editarContrato($id)
    {
        $contrato = Contrato::findOrFail($id);

        if ($contrato->estado !== 'pendiente') {
            session()->flash('error', 'No es posible editar un contrato que no esté en estado pendiente.');
            return;
        }

        return redirect()->route('contratos.edit', $id);
    }

    protected function rules(): array
    {
        return [
            'barrio_id'            => ['nullable', 'integer', 'exists:barrios,id'],
            'estado'               => ['nullable', 'string', 'in:pendiente,verificado,aprobado,rechazado,finalizado,anulado'],
            'numero_contrato'      => ['nullable', 'string', 'max:100'],
            'monto_min'            => ['nullable', 'numeric', 'min:0'],
            'monto_max'            => ['nullable', 'numeric', 'min:0', 'gte:monto_min'],
            'fecha_inicio'         => ['nullable', 'date'],
            'fecha_fin'            => ['nullable', 'date', 'after_or_equal:fecha_inicio'],
            'rol_ingreso'          => ['nullable', 'string'],
            'rol_verificacion'     => ['nullable', 'string'],
            'rol_aprobacion'       => ['nullable', 'string'],
            'con_blockchain'       => ['nullable', 'in:0,1'],
            'fecha_ingreso_inicio' => ['nullable', 'date'],
            'fecha_ingreso_fin'    => ['nullable', 'date', 'after_or_equal:fecha_ingreso_inicio'],
        ];
    }

    private function buildQuery()
    {
        $query = Contrato::with('barrio');

        // (Se mantienen todos tus filtros exactos de búsqueda estables...)
        if ($this->barrio_id) $query->where('barrio_id', $this->barrio_id);
        if ($this->estado !== '') $query->where('estado', $this->estado);
        if ($this->numero_contrato !== '') $query->where('numero_contrato', 'like', '%' . $this->numero_contrato . '%');
        if ($this->monto_min !== null) $query->where('monto_total', '>=', $this->monto_min);
        if ($this->monto_max !== null) $query->where('monto_total', '<=', $this->monto_max);
        if ($this->fecha_inicio !== '') $query->whereDate('fecha_inicio', '>=', $this->fecha_inicio);
        if ($this->fecha_fin !== '') $query->whereDate('fecha_fin', '<=', $this->fecha_fin);
        if ($this->rol_ingreso !== '') $query->where('rol_ingreso', $this->rol_ingreso);
        if ($this->rol_verificacion !== '') $query->where('rol_verificacion', $this->rol_verificacion);
        if ($this->rol_aprobacion !== '') $query->where('rol_aprobacion', $this->rol_aprobacion);

        if ($this->con_blockchain !== '') {
            (bool) $this->con_blockchain ? $query->whereNotNull('blockchain_tx') : $query->whereNull('blockchain_tx');
        }
        if ($this->fecha_ingreso_inicio !== '') $query->whereDate('fecha_ingreso', '>=', $this->fecha_ingreso_inicio);
        if ($this->fecha_ingreso_fin !== '') $query->whereDate('fecha_ingreso', '<=', $this->fecha_ingreso_fin);

        return $query->orderBy($this->sortField, $this->sortDirection);
    }

    public function render()
    {
        return view('livewire.admin.contratos.index', [
            'contratos' => $this->buildQuery()->paginate($this->perPage),
            'barrios'   => Barrio::orderBy('nombre')->get(['id', 'nombre']),
            'roles'     => Role::orderBy('name')->get(['id', 'name']),
        ]);
    }
}
