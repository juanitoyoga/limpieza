<?php

namespace App\Livewire\Admin\Denuncias;

use App\Models\Barrio;
use App\Models\Ordenanza332;
use App\Models\Role;
use App\Models\Vecino;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.admin')]

class Index extends Component
{
    public $vecino_id = '';
    public $barrio_id = '';
    public $ordenanza332_id = '';

    public $fecha_inicio = '';
    public $fecha_fin = '';

    public $estado_revision = ''; // verificado | aprobado | rechazado
    public $fecha_revision_inicio = '';
    public $fecha_revision_fin = '';

    public $rol = ''; // role_name que aplicó la revisión

    public function buscar()
    {
        return $this->redirectRoute('denuncias.lista', [
            'vecino_id'             => $this->vecino_id,
            'barrio_id'             => $this->barrio_id,
            'ordenanza332_id'       => $this->ordenanza332_id,
            'fecha_inicio'          => $this->fecha_inicio,
            'fecha_fin'             => $this->fecha_fin,
            'estado_revision'       => $this->estado_revision,
            'fecha_revision_inicio' => $this->fecha_revision_inicio,
            'fecha_revision_fin'    => $this->fecha_revision_fin,
            'rol'                   => $this->rol,
        ]);


        \Log::info('Filtros de denuncias actualizados', $filtros);

        $this->dispatch('filtrosActualizados', ...$filtros);
    }

    public function render()
    {
        return view('livewire.admin.denuncias.index', [
            'vecinos' => Vecino::with('user')
                ->join('users', 'vecinos.user_id', '=', 'users.id')
                ->orderBy('users.first_name')
                ->orderBy('users.last_name')
                ->select('vecinos.*')
                ->get(),
            'barrios'         => Barrio::activos()->orderBy('nombre')->get(),
            'contravenciones' => Ordenanza332::orderBy('codigo')->get(),
            'roles'           => Role::ordered()->get(),
        ]);
    }
}
