<?php

namespace App\Livewire\Operacion\Nominations;

use Livewire\Component;

use Livewire\Attributes\Layout;

use Illuminate\Support\Carbon;

use App\Models\Nomination;
use App\Models\User;
use App\Models\Role;


#[Layout('layouts.operacion')]

class Show extends Component
{

    protected Nomination $nomination;

    public $users;
    public $roles;
    public $instituciones;

    public $candidato;
    public $role_name;
    public $released_by;
    public $issuer_type;
    public $pdf;
    public $fecha_emision;
    public $fecha_inicio_vigencia;
    public $fecha_fin_vigencia;
    public $observaciones;
    public function mount($id)
    {
        $this->nomination = Nomination::with([
            'nominator',
            'candidate',
            'verifier',
            'approver'
        ])->findOrFail($id);


        // Cargar valores existentes
        $this->candidato = $this->nomination->candidate_user_id . ' ' . $this->nomination->candidate->first_name . ' ' . $this->nomination->candidate->last_name;
        $this->role_name = $this->nomination->role_name;
        $this->released_by = $this->nomination->released_by;
        $this->issuer_type = $this->nomination->issuer_type;
        $this->pdf = $this->nomination->document_path;
        $this->fecha_emision = Carbon::parse($this->nomination->fecha_emision)
                ->locale('es')
                ->translatedFormat('d - F - Y');

        $this->fecha_inicio_vigencia = Carbon::parse($this->nomination->fecha_inicio_vigencia)
            ->locale('es')
            ->translatedFormat('d - F - Y');
        
        $this->fecha_fin_vigencia = Carbon::parse($this->nomination->fecha_fin_vigencia)
            ->locale('es')
            ->translatedFormat('d - F - Y');
        $this->observaciones = $this->nomination->observaciones;
    }
    public function render()
    {
        return view('livewire.operacion.nominations.show');
    }

    public function save() 
    {

        return redirect()->route('nominations.index');
    }
}
