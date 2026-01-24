<?php

namespace App\Livewire\Operacion\Nominations;

use App\Models\Nomination;

use App\Models\User;

use App\Models\Role;

use App\Models\AuditEvent;

use App\Models\Barrio;

use App\Models\Departamento;

use Illuminate\Support\Facades\Auth;

use Livewire\Component;

use Livewire\WithFileUploads;

use Livewire\Attributes\Layout;

use Illuminate\Support\Facades\Storage;

use Illuminate\Support\Facades\DB;

#[Layout('layouts.operacion')]
class Search extends Component
{
    use WithFileUploads;

    // === FORM DATA ===
    public int $candidate_user_id;
    public string $role_name;
    public string $issuer_type = 'DMQ';
    public ?string $observaciones = null;

    public $nomination;
    public $pdf;

    public $fecha_emision;
    public $fecha_inicio_vigencia;
    public $fecha_fin_vigencia;

    public $released_by;

    public $instituciones = [];
    public $selectedInstitucion;    
    
    protected $rules = [
        'candidate_user_id' => 'required',
        'role_name' => 'required',
        'issuer_type' => 'required',
        'pdf' => 'required|file|mimes:pdf',
        'fecha_emision' => 'required|date',
        'fecha_inicio_vigencia' => 'required|date',
        'fecha_fin_vigencia' => 'required|date|after_or_equal:fecha_inicio',
        'released_by'=> 'required|string|max:50',
        'observaciones' => 'nullable|string|max:1000',
        ];
    

    public function render()
    {
        return view('livewire.operacion.nominations.create', [
            'users' => User::whereIn('role', ['User', 'Vecino'])
                            ->orderBy('last_name')
                            ->get(['id', 'last_name', 'first_name', 'role']),
            
            'roles' => Role::whereIn('name',['Funcionario', 'Supervisor', 'Auditor', 'Dirigente', 'Presidente'])->orderBy('name')->get(),
        ]);
    }
    
    // === ACTION ===
    public function save()
    {
        $this->validate();

        $this->nomination =
        DB::transaction(function () {

            $user_Id = Auth::id();

            /** 1️⃣ Crear la nominación */
            $nomination = Nomination::create([
                'nominator_id' => $user_Id,
                'candidate_user_id' => $this->candidate_user_id,
                
                'issuer_type' => $this->issuer_type,
                'fecha_emision' => $this->fecha_emision,
                'fecha_inicio_vigencia' => $this->fecha_inicio_vigencia,
                'fecha_fin_vigencia' => $this->fecha_fin_vigencia,
                'released_by' => $this->released_by,
                'estado' => Nomination::ESTADO_PROPUESTA,
                'observaciones' => $this->observaciones,
                'is_active' => true,
                'role_name' =>$this->role_name,
            ]);

            /** 2️⃣ Directorio por rol */
 
            $directory = $nomination->nominationDirectory($this->issuer_type);




            /** 3️⃣ Guardar PDF */
            $filename = $nomination->numero_tramite . '.pdf';

            $path = $this->pdf->storeAs(
                $directory,
                $filename,
                'nominations'
            );

            /** 4️⃣ Hash del PDF */
            $absolutePath = Storage::disk('nominations')->path($path);
            $hash = hash_file('sha256', $absolutePath);

            /** 5️⃣ Actualizar nominación */
            $nomination->update([
                'document_path' => $path,
                'hash_reference' => $hash,
                'role_name' => $this->role_name,
            ]);

            /** 6️⃣ Auditoría: creación */
            AuditEvent::logEvent(
                $nomination->id,
                $user_Id,
                AuditEvent::EVENT_NOMINATION_CREATED,
                [
                    'numero_tramite' => $nomination->numero_tramite,
                    'role' => $this->role_name,
                ]
            );

            /** 7️⃣ Auditoría: documento */
            AuditEvent::logEvent(
                $nomination->id,
                $user_Id,
                AuditEvent::EVENT_DOCUMENT_UPLOADED,
                [
                    'path' => $path,
                    'hash' => $hash,
                ]
            );
            return $nomination;
        });
        session()->flash(
            'success',
            'Nominación creada correctamente. Imprima el documento para firma y sello.'
        );
        
        return redirect()->route('nominations.imprimir', $this->nomination->id);       
        
    }

    public function rolCambiado()
    {
        if (in_array($this->role_name, ['Funcionario', 'Supervisor', 'Auditor'])) {
            $this->instituciones = Departamento::select(
                'id as numero',
                'name as nombre'
            )->get();
        } elseif (in_array($this->role_name, ['Dirigente', 'Presidente'])) {
            $this->instituciones = Barrio::select(
                'id as numero',
                'nombre as nombre'
            )->get();
        } else {
            $this->instituciones = [];
        }

 
        $this->selectedInstitucion = null; // Resetea la institución seleccionada
    }
    
}
