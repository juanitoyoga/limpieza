<?php

namespace App\Livewire\Operacion\Nominations;

use App\Models\{Nomination, User, AuditEvent, Supervisor, Auditor, Funcionario};
use Illuminate\Support\Facades\{Auth, DB};
use Illuminate\Support\Carbon;
use Livewire\Component;
use Livewire\Attributes\{Layout, Locked};

#[Layout('layouts.operacion')]
class Aprobar extends Component
{
    #[Locked]
    public $nominationId;

    public $observaciones;
    public $acepta_responsabilidad = false;
    public $details = [];
    public $isFirstTimeMode;

    protected $rules = [
        'acepta_responsabilidad' => 'accepted',
        'observaciones'          => 'required|min:10|string',
    ];

    public function mount($id)
    {
        $nomination = Nomination::with(['candidate', 'nominator', 'verifier'])->findOrFail($id);
        $this->nominationId = $nomination->id;

        // Detectamos si es el caso especial para mostrar la alerta en la vista
        $currentUser = Auth::user();
        $this->isFirstTimeMode = (
            \App\Models\Auditor::count() === 0 &&
            $currentUser->role_name === 'SuperAdmin' &&
            $currentUser->transition_role === 'Auditor' &&
            $nomination->role_name === 'Auditor'
        );

        $this->details = [
            'candidate_id'   => $nomination->candidate_user_id,
            'candidate_name' => "{$nomination->candidate->last_name} {$nomination->candidate->first_name}",
            'nominator_id'   => $nomination->nominator_id,
            'nominator_name' => "{$nomination->nominator->last_name} {$nomination->nominator->first_name}",
            'verifier_id'    => $nomination->verified_by,
            'verifier_name'  => $nomination->verifier ? "{$nomination->verifier->last_name} {$nomination->verifier->first_name}" : 'N/A',
            'role_name'      => $nomination->role_name,
            'released_by'    => $nomination->released_by,
            'pdf'            => $nomination->document_path,
            'obs_previa'     => $nomination->observaciones,
            'fechas' => [
                // Nuevas fechas solicitadas
                'registro'       => $nomination->created_at->translatedFormat('d - F - Y, H:i'),
                'emision'        => Carbon::parse($nomination->fecha_emision)->translatedFormat('d - F - Y'),
                'inicio'         => Carbon::parse($nomination->fecha_inicio_vigencia)->translatedFormat('d - F - Y'),
                'fin'            => Carbon::parse($nomination->fecha_fin_vigencia)->translatedFormat('d - F - Y'),
                'verificacion'   => $nomination->verified_at ? Carbon::parse($nomination->verified_at)->translatedFormat('d - F - Y') : 'No verificada',
            ]
        ];
    }

    public function save()
    {
        $this->validate();

        $nomination = Nomination::findOrFail($this->nominationId);
        /** @var User $currentUser */
        $currentUser = Auth::user();

        // 1. RESTRICCIONES DE SEGURIDAD
        if ($currentUser->id === $nomination->nominator_id) {
            return $this->addError('security', 'El creador de la nominación no puede ser el aprobador.');
        }
        if ($currentUser->id === $nomination->verified_by) {
            return $this->addError('security', 'El verificador no puede ser el mismo aprobador.');
        }
        if ($currentUser->id === $nomination->candidate_user_id) {
            return $this->addError('security', 'El candidato no puede aprobar su propia nominación.');
        }

        DB::transaction(function () use ($nomination, $currentUser) {
            $now = now();

            // Determinamos si es Primera Vez (Caso Auditor Inicial)
            $isFirstTimeAuditor = $this->checkFirstTimeAuditor($nomination, $currentUser);

            // 2. ACTUALIZACIÓN DE LA NOMINACIÓN
            $nomination->update([
                'approved_by'   => $currentUser->id,
                'approved_at'   => $now,
                'observaciones' => trim($nomination->observaciones . " | APROBACIÓN: " . $this->observaciones),
                'estado'        => Nomination::ESTADO_APROBADA,
            ]);

            // 3. LÓGICA DE REGISTRO SEGÚN ROL Y ESCENARIO
            if ($isFirstTimeAuditor) {
                $this->processFirstTimeAuditor($nomination, $currentUser);
            } else {
                $this->processNormalOperation($nomination);
            }

            // 4. EVENTO DE AUDITORÍA GLOBAL
            AuditEvent::logEvent(
                $nomination,
                $currentUser->id,
                AuditEvent::EVENT_APPROVAL_GRANTED,
                ['message' => $isFirstTimeAuditor ? 'Aprobación inicial de sistema (Primer Auditor)' : 'Aprobación en flujo normal']
            );
        });

        session()->flash('success', 'Nominación aprobada y registro de rol completado.');

        return redirect()->route('nominations.imprimir', $nomination->id);
    }

    private function checkFirstTimeAuditor($nomination, $user): bool
    {
        return Auditor::count() === 0
            && $user->role_name === 'SuperAdmin'
            && $user->transition_role === 'Auditor'
            && $nomination->role_name === 'Auditor'
            && !is_null($nomination->verified_at);
    }

    private function processFirstTimeAuditor($nomination, $user)
    {
        // Crear el registro en la tabla de Auditores
        Auditor::create([
            'user_id'       => $nomination->candidate_user_id,
            'nomination_id' => $nomination->id,
            'is_active'     => 1,
            'email'         => $nomination->candidate->email,
            'dependencia_dmq'   => $nomination->released_by,
            'referencias'       => $nomination->observaciones,
            'password'          => $nomination->candidate->password,
        ]);

        // Actualizar al SuperAdmin que aprueba
        $user->update(['transition_role' => 'SuperAdmin']);

        // Actualizar al candidato (ahora es Auditor)
        $nomination->candidate->update(['role_name' => 'Auditor']);
    }

    private function processNormalOperation($nomination)
    {
        $candidate = $nomination->candidate;
        $role = $nomination->role_name;

        // Actualizar rol del usuario
        $candidate->update(['role_name' => $role]);

        match ($role) {
            'Auditor'     => Auditor::create([
                'user_id'         => $candidate->id,
                'nomination_id'   => $nomination->id,
                'is_active'       => 1,
                'email'           => $candidate->email,
                'dependencia_dmq' => $nomination->released_by,
                'referencias'     => $nomination->observaciones,
                'password'        => $candidate->password,
            ]),
            'Supervisor'  => Supervisor::create([
                'user_id'         => $candidate->id,
                'nomination_id'   => $nomination->id,
                'is_active'       => 1,
                'email'           => $candidate->email,
                'dependencia_dmq' => $nomination->released_by,
                'referencias'     => $nomination->observaciones,
                'password'        => $candidate->password,
            ]),
            'Funcionario' => Funcionario::create([
                'user_id'         => $candidate->id,
                'nomination_id'   => $nomination->id,
                'is_active'       => 1,
                'email'           => $candidate->email,
                'dependencia_dmq' => $nomination->released_by,
                'referencias'     => $nomination->observaciones,
                'password'        => $candidate->password,
            ]),
            'Dirigente'  => \App\Models\Dirigente::create([
                'user_id'       => $candidate->id,
                'barrio_id'     => $this->resolveBarrioId($nomination->released_by),
                'nomination_id' => $nomination->id,
                'email'         => $candidate->email,
                'role_name'     => 'Dirigente',
                'password'      => $candidate->password,
                'is_active'     => true,
                'referencias'   => $nomination->observaciones,
            ]),
            'Presidente' => \App\Models\Presidente::create([
                'user_id'       => $candidate->id,
                'barrio_id'     => $this->resolveBarrioId($nomination->released_by),
                'nomination_id' => $nomination->id,
                'email'         => $candidate->email,
                'role_name'     => 'Presidente',
                'password'      => $candidate->password,
                'is_active'     => true,
                'referencias'   => $nomination->observaciones,
            ]),
            default => throw new \Exception("Rol de nominación no reconocido para registro."),
        };
    }

    private function resolveBarrioId(string $nombreBarrio): int
    {
        $barrio = \App\Models\Barrio::where('nombre', $nombreBarrio)->first();

        if (!$barrio) {
            throw new \Exception("No se encontró el barrio '{$nombreBarrio}' para asignar al Dirigente/Presidente.");
        }

        return $barrio->id;
    }

    public function render()
    {
        return view('livewire.operacion.nominations.aprobar');
    }
}
