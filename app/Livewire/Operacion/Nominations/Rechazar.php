<?php

namespace App\Livewire\Operacion\Nominations;

use App\Models\Nomination;

use App\Models\User;

use App\Models\AuditEvent;

use Illuminate\Support\Facades\Auth;

use Livewire\Component;

use Livewire\WithFileUploads;

use Livewire\Attributes\Layout;

use Illuminate\Support\Facades\DB;

#[Layout('layouts.operacion')]
class Rechazar extends Component
{
    use WithFileUploads;

    // === FORM DATA ===
    public Nomination $nomination;
    public $candidate_user_id;
    public $role_name;
    public $released_by;
    public $issuer_type;
    public $fecha_emision;
    public $fecha_inicio_vigencia;
    public $fecha_fin_vigencia;
    public $observaciones;
    public $estado_nominacion;
    public $users = [];
    public $roles = [];
    public $instituciones = [];
    public $rejected_at;
    public $approved_at;
    public $acepta_responsabilidad = false;
    public $candidate_name;
    public $nominator_name;
    public $approver_name;
    public $verifier_name;
    
    
    protected $rules = [
 
        'rejected_at' => 'required|date',
 
        ];
    
        public function mount(int $nomination)
        {
            $this->nomination = Nomination::with([
                'candidate:id,first_name,last_name',
                'nominator:id,first_name,last_name',
                'verifier:id,first_name, last_name',
                'approver:id, first_name, last_name',
            ])->findOrFail($nomination);
        
            // Nombres legibles
            $this->candidate_name = $this->nomination->candidate->last_name
                .' '.$this->nomination->candidate->first_name;
        
            $this->nominator_name = $this->nomination->nominator->last_name
                .' '.$this->nomination->nominator->first_name;
                
            $this->verifier_name = $this->nomination->verifier->last_name
                .' '.$this->nomination->verifier->first_name;
            
            $this->approver_name = $this->nomination->approver->last_name
                .' '.$this->nomination->approver->first_name;
        
            // Campos solo lectura
            $this->role_name             = $this->nomination->role_name;
            $this->released_by           = $this->nomination->released_by;
            $this->issuer_type           = $this->nomination->issuer_type;
            $this->fecha_emision         = $this->nomination->fecha_emision;
            $this->fecha_inicio_vigencia = $this->nomination->fecha_inicio_vigencia;
            $this->fecha_fin_vigencia    = $this->nomination->fecha_fin_vigencia;
            $this->observaciones         = $this->nomination->observaciones;
        }
        
        
    public function render()
    {
        return view('livewire.operacion.nominations.rechazar', 
        );
    }
    
    // === ACTION ===

    public function save()
    {

            $this->validate();
  
            $this->nomination = DB::transaction(function () {
                /** @var User $user */
                $user = User::find(Auth::id());
                $user_Id = $user->id;
    
                if ($user_Id === $this->nomination->nominator_id){
                    session()->flash('message', [
                        'type' => 'error',
                        'text' => 'Nominación no puede ser rechazada correctamente.'
                    ]);
 
                    return redirect()->route('nominations.index');
                }
                if(!$this->nomination->estado === 'verificada'){
                    session()->flash('message', [
                        'type' => 'error',
                        'text' => 'Nominación no puede ser rechazada correctamente.'
                    ]);
 
                    return redirect()->route('nominations.index');
                }

                $this->estado_nominacion = Nomination::ESTADO_RECHAZADA;
                // 2. Crear la nominación
                $nomination = Nomination::update([
                    'rejected_by' => $user_Id,
                    'rejected_at' => $this->rejected_at,
                    'estado' => $this->estado_nominacion,
                    'observaciones' => $this->observaciones,

                ]);

                // Auditoría: Aprobación Automática
                AuditEvent::logEvent($this->nomination->id, $user_Id, AuditEvent::EVENT_APPROVAL_REJECTED, [
                    'message' => 'Nominacion rechazada'
                ]);
    
                    return $nomination;
    
            });
    
            session()->flash('success', 'Nominación verificada correctamente.');
            return redirect()->route('nominations.imprimir', $this->nomination->id);
    
 
    }

    
}
