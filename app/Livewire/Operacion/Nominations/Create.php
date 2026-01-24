<?php

namespace App\Livewire\Operacion\Nominations;

use App\Models\{Nomination, User, Role, AuditEvent, Barrio, Departamento, Funcionario, Supervisor, Auditor};
use Illuminate\Support\Facades\{Auth, Storage, DB, Log, Gate};
use Livewire\{Component, WithFileUploads};
use Livewire\Attributes\Layout;

#[Layout('layouts.operacion')]
class Create extends Component
{
    use WithFileUploads;

    // Propiedades del Formulario
    public $candidate_user_id, $role_name, $issuer_type = 'DMQ', $observaciones;
    public $pdf, $fecha_emision, $fecha_inicio_vigencia, $fecha_fin_vigencia, $released_by;

    // Propiedades de Apoyo
    public $instituciones = [];
    public $isFirstTime = false;

    protected $rules = [
        'candidate_user_id'     => 'required|exists:users,id',
        'role_name'             => 'required|exists:roles,name',
        'issuer_type'           => 'required|in:DMQ,JUNTA_PARROQUIAL',
        'pdf'                   => 'required|file|mimes:pdf|max:5120', // Max 5MB
        'fecha_emision'         => 'required|date|before_or_equal:today',
        'fecha_inicio_vigencia' => 'required|date',
        'fecha_fin_vigencia'    => 'required|date|after:fecha_inicio_vigencia',
        'released_by'           => 'required|string|max:100',
        'observaciones'         => 'nullable|string|max:1000',
    ];

    public function mount()
    {
        Gate::authorize('nominations.create');
        $this->checkFirstTimeStatus();
    }

    public function checkFirstTimeStatus()
    {
        /** @var User $user */
        $user = Auth::user();
        $tablesEmpty = Funcionario::count() === 0 && Supervisor::count() === 0 && Auditor::count() === 0;
        
        $this->isFirstTime = $tablesEmpty && 
                             $user->transition_role === 'Funcionario' && 
                             $user->role_name === 'SuperAdmin';
    }

    public function updatedRoleName()
    {
        if (in_array($this->role_name, ['Funcionario', 'Supervisor', 'Auditor'])) {
            $this->instituciones = Departamento::orderBy('name')->get(['name as nombre']);
        } elseif (in_array($this->role_name, ['Dirigente', 'Presidente'])) {
            $this->instituciones = Barrio::orderBy('nombre')->get(['nombre']);
        } else {
            $this->instituciones = [];
        }
        $this->released_by = null; // Resetear selección al cambiar rol
    }

    public function save()
    {
        $this->validate();

        try {
            $nomination = DB::transaction(function () {
                $user = Auth::user();
                $estado = $this->isFirstTime ? Nomination::ESTADO_APROBADA : Nomination::ESTADO_PROPUESTA;

                // 1. Crear la nominación base
                $nomination = Nomination::create([
                    'nominator_id'          => $user->id,
                    'candidate_user_id'     => $this->candidate_user_id,
                    'role_name'             => $this->role_name,
                    'issuer_type'           => $this->issuer_type,
                    'fecha_emision'         => $this->fecha_emision,
                    'fecha_inicio_vigencia' => $this->fecha_inicio_vigencia,
                    'fecha_fin_vigencia'    => $this->fecha_fin_vigencia,
                    'released_by'           => $this->released_by,
                    'estado'                => $estado,
                    'observaciones'         => $this->observaciones,
                    'verified_by'           => $this->isFirstTime ? $user->id : null,
                    'verified_at'           => $this->isFirstTime ? now() : null,
                    'approved_by'           => $this->isFirstTime ? $user->id : null,
                    'approved_at'           => $this->isFirstTime ? now() : null,
                ]);

                // 2. Procesar Archivo
                $directory = $nomination->nominationDirectory($this->issuer_type);
                $filename = "{$nomination->numero_tramite}.pdf";
                $path = $this->pdf->storeAs($directory, $filename, 'nominations');
                $hash = hash_file('sha256', Storage::disk('nominations')->path($path));

                $nomination->update([
                    'document_path'  => $path,
                    'hash_reference' => $hash,
                ]);

                // 3. Auditoría inicial
                $this->logNominationEvents($nomination, $user->id, $path, $hash);

                // 4. Lógica Especial de Primera Vez
                if ($this->isFirstTime) {
                    $this->handleFirstTimeActivation($nomination, $user, $path);
                }

                return $nomination;
            });

            session()->flash('success', 'Nominación registrada exitosamente.');
            return redirect()->route('nominations.imprimir', $nomination->id);

        } catch (\Exception $e) {
            Log::error("Error creando nominación: " . $e->getMessage());
            $this->addError('global', 'Error crítico en el servidor. Intente nuevamente.');
        }
    }

    private function handleFirstTimeActivation($nomination, $user, $path)
    {
        // Actualizar SuperAdmin
        $user->update(['transition_role' => 'Supervisor']);

        // Actualizar Candidato
        $candidate = User::find($this->candidate_user_id);
        $candidate->update(['role_name' => 'Funcionario']);

        // Crear Fila Funcionario
        Funcionario::create([
            'user_id'         => $candidate->id,
            'nomination_id'   => $nomination->id,
            'is_active'       => 1,
            'email'           => $candidate->email,
            'dependencia_dmq' => $nomination->released_by,
            'referencias'     => json_encode(['tramite' => $nomination->numero_tramite, 'path' => $path]),
        ]);
    }

    private function logNominationEvents($nomination, $userId, $path, $hash)
    {
        AuditEvent::logEvent($nomination->id, $userId, AuditEvent::EVENT_NOMINATION_CREATED, ['role' => $this->role_name]);
        AuditEvent::logEvent($nomination->id, $userId, AuditEvent::EVENT_DOCUMENT_UPLOADED, ['hash' => $hash]);

        if ($this->isFirstTime) {
            AuditEvent::logEvent($nomination->id, $userId, AuditEvent::EVENT_VERIFICATION_COMPLETED, ['msg' => 'Auto-verificación inicial']);
            AuditEvent::logEvent($nomination->id, $userId, AuditEvent::EVENT_APPROVAL_GRANTED, ['msg' => 'Auto-aprobación inicial']);
        }
    }

    public function render()
    {
        return view('livewire.operacion.nominations.create', [
            'users' => User::whereIn('role_name', ['User', 'Vecino'])->orderBy('last_name')->get(),
            'roles' => Role::whereIn('name', ['Funcionario', 'Supervisor', 'Auditor', 'Dirigente', 'Presidente'])->orderBy('name')->get(),
        ]);
    }
}