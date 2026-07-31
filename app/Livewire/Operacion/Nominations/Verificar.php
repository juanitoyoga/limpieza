<?php

namespace App\Livewire\Operacion\Nominations;

use App\Models\{Nomination, User, AuditEvent, Supervisor, Auditor};
use Illuminate\Support\Facades\{Auth, DB, Log};
use Illuminate\Support\Carbon;
use Livewire\Component;
use Livewire\Attributes\{Layout, Locked};

#[Layout('layouts.operacion')]
class Verificar extends Component
{
    #[Locked]
    public $nominationId;

    public $observaciones;
    public $acepta_responsabilidad = false;
    public $details = [];

    protected function rules()
    {
        return [
            'acepta_responsabilidad' => 'accepted',
            'observaciones'          => 'required|min:5|string',
        ];
    }

    public function mount($id)
    {
        $nomination = Nomination::with(['candidate', 'nominator'])->findOrFail($id);
        $this->nominationId = $nomination->id;

        $this->details = [
            'candidate_name' => "{$nomination->candidate->last_name} {$nomination->candidate->first_name}",
            'nominator_name' => "{$nomination->nominator->last_name} {$nomination->nominator->first_name}",
            'role_name'      => $nomination->role_name,
            'released_by'    => $nomination->released_by,
            'pdf'            => $nomination->document_path,
            'obs_previa'     => $nomination->observaciones,
            'fechas' => [
                'emision' => Carbon::parse($nomination->fecha_emision)->translatedFormat('d - F - Y'),
                'inicio'  => Carbon::parse($nomination->fecha_inicio_vigencia)->translatedFormat('d - F - Y'),
                'fin'     => Carbon::parse($nomination->fecha_fin_vigencia)->translatedFormat('d - F - Y'),
            ]
        ];
    }

    public function save()
    {
        $this->validate();

        $nomination = Nomination::findOrFail($this->nominationId);
        $currentUser = Auth::user();

        if ($currentUser->id === $nomination->nominator_id) {
            session()->flash('error', 'No puedes verificar una nominación creada por ti mismo.');
            return;
        }

        try {
            DB::beginTransaction();

            $isSpecialCase = $this->checkSpecialCase($nomination, $currentUser);
            $estado = $isSpecialCase ? Nomination::ESTADO_APROBADA : Nomination::ESTADO_VERIFICADA;
            $now = now();

            // 1. Actualizar Nominación
            $nomination->update([
                'verified_by'   => $currentUser->id,
                'verified_at'   => $now,
                'approved_by'   => $isSpecialCase ? $currentUser->id : $nomination->approved_by,
                'approved_at'   => $isSpecialCase ? $now : $nomination->approved_at,
                'observaciones' => trim(($nomination->observaciones ?? '') . " | VERIF: " . $this->observaciones),
                'estado'        => $estado,
            ]);

            // 2. Log de Auditoría
            AuditEvent::logEvent(
                $nomination,
                $currentUser->id,
                AuditEvent::EVENT_VERIFICATION_COMPLETED,
                ['message' => 'Verificación realizada con observaciones: ' . $this->observaciones]
            );

            // 3. Caso especial (Activación inmediata)
            if ($isSpecialCase) {
                $this->processSpecialActivation($nomination, $currentUser);
            }

            DB::commit();
            session()->flash('success', 'Nominación verificada correctamente.');
            return redirect()->route('nominations.imprimir', $nomination->id);
        } catch (\Exception $e) {
            DB::rollBack();

            // Registramos el error técnico en el log de Laravel (storage/logs/laravel.log)
            Log::error("Error en Verificación de Nominación ID {$this->nominationId}: " . $e->getMessage(), [
                'exception' => $e,
                'user_id' => Auth::id()
            ]);

            session()->flash('error', 'Ocurrió un error crítico al procesar la verificación. Por favor, contacte a soporte técnico.');
        }
    }

    private function checkSpecialCase($nomination, $user): bool
    {
        // Nota: Asegúrate que el campo en tu tabla User es 'role' o 'role_name'
        $tablesEmpty = Supervisor::count() === 0 && Auditor::count() === 0;
        $isSpecialUser = ($user->transition_role === 'Supervisor' && ($user->role === 'SuperAdmin' || $user->role_name === 'SuperAdmin'));
        $isSupervisorNomination = ($nomination->role_name === 'Supervisor');
        // dd($tablesEmpty, $isSpecialUser, $isSupervisorNomination);
        return $tablesEmpty && $isSpecialUser && $isSupervisorNomination;
    }

    private function processSpecialActivation($nomination, $user)
    {
        // Actualizar al SuperAdmin verificador
        $user->update(['transition_role' => 'Auditor']);

        // Actualizar al candidato (Cargamos el modelo para asegurar persistencia)
        $candidate = User::findOrFail($nomination->candidate_user_id);

        // ¡IMPORTANTE! Asegúrate de que la columna se llame 'role' o 'role_name' en tu tabla users
        $candidate->update(['role_name' => 'Supervisor']);

        // Crear registro en tabla de Supervisores
        Supervisor::create([
            'user_id'         => $candidate->id,
            'role_name'       => 'Supervisor',
            'password'        => $candidate->password, // Usamos el pass del candidato, no del verificador
            'nomination_id'   => $nomination->id,
            'is_active'       => 1,
            'email'           => $candidate->email,
            'dependencia_dmq' => $nomination->released_by,
            'referencias'     => json_encode([
                'numero_tramite' => $nomination->numero_tramite,
                'path'           => $nomination->document_path
            ]),
            'last_login_at'   => $candidate->last_login_at,
        ]);

        AuditEvent::logEvent($nomination, $user->id, AuditEvent::EVENT_APPROVAL_GRANTED, [
            'message' => 'Aprobación automática y creación de Supervisor por arranque inicial.'
        ]);
    }

    public function render()
    {
        return view('livewire.operacion.nominations.verificar');
    }
}
