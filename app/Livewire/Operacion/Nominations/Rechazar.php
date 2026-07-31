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
    public $details = [];
    public $rejected_at;
    public $approved_at;
    public $acepta_responsabilidad = false;
    public $candidate_name;
    public $nominator_name;
    public $approver_name;
    public $verifier_name;
    public $rejecter_name;

    protected function rules()
    {
        return [
            'acepta_responsabilidad' => 'accepted',
            'observaciones'          => 'required|min:5|string',
        ];
    }

    public function mount(int $id)
    {
        logger()->info("Rechazar::mount — Cargando nominación", [
            'nomination_id' => $id,
            'user_id'       => Auth::id(),
        ]);

        $this->nomination = Nomination::with([
            'candidate:id,first_name,last_name',
            'nominator:id,first_name,last_name',
            'verifier:id,first_name,last_name',
            'approver:id,first_name,last_name',
            'rejecter:id,first_name,last_name',
        ])->findOrFail($id);

        logger()->info("Rechazar::mount — Nominación cargada correctamente", [
            'estado' => $this->nomination->estado,
            'role_name' => $this->nomination->role_name,
        ]);

        // Nombres legibles
        $this->candidate_name = $this->nomination->candidate->last_name
            . ' ' . $this->nomination->candidate->first_name;

        $this->nominator_name = $this->nomination->nominator->last_name
            . ' ' . $this->nomination->nominator->first_name;

        $this->verifier_name = $this->nomination->verifier
            ? $this->nomination->verifier->last_name . ' ' . $this->nomination->verifier->first_name
            : null;

        $this->approver_name = $this->nomination->approver
            ? $this->nomination->approver->last_name . ' ' . $this->nomination->approver->first_name
            : null;

        $this->rejecter_name = $this->nomination->rejecter
            ? $this->nomination->rejecter->last_name . ' ' . $this->nomination->rejecter->first_name
            : null;

        // Campos solo lectura
        $this->role_name             = $this->nomination->role_name;
        $this->released_by           = $this->nomination->released_by;
        $this->issuer_type           = $this->nomination->issuer_type;
        $this->fecha_emision         = $this->nomination->fecha_emision;
        $this->fecha_inicio_vigencia = $this->nomination->fecha_inicio_vigencia;
        $this->fecha_fin_vigencia    = $this->nomination->fecha_fin_vigencia;
        $this->observaciones         = $this->nomination->observaciones;

        // Datos para la vista
        $this->details = [
            'candidate_name'  => $this->candidate_name,
            'nominator_name'  => $this->nominator_name,
            'verifier_name'   => $this->verifier_name,
            'approver_name'   => $this->approver_name,
            'rejecter_name'   => $this->rejecter_name,
            'role_name'       => $this->role_name,
            'released_by'     => $this->released_by,
            'pdf'             => $this->nomination->document_path,
            'obs_previa'      => $this->observaciones,
            'fechas' => [
                'emision' => \Illuminate\Support\Carbon::parse($this->fecha_emision)->translatedFormat('d - F - Y'),
                'inicio'  => \Illuminate\Support\Carbon::parse($this->fecha_inicio_vigencia)->translatedFormat('d - F - Y'),
                'fin'     => \Illuminate\Support\Carbon::parse($this->fecha_fin_vigencia)->translatedFormat('d - F - Y'),
            ],
        ];
    }

    public function render()
    {
        return view('livewire.operacion.nominations.rechazar');
    }

    // === ACTION ===

    public function save()
    {
        $this->validate();

        try {
            DB::transaction(function () {
                /** @var User $user */
                $user = User::find(Auth::id());
                $user_Id = $user->id;

                if ($user_Id === $this->nomination->nominator_id) {
                    throw new \RuntimeException('El nominador no puede rechazar su propia nominación.');
                }

                if ($this->nomination->estado !== 'verificada') {
                    throw new \RuntimeException('Nominación no puede ser rechazada: estado actual es "' . $this->nomination->estado . '".');
                }

                $this->estado_nominacion = Nomination::ESTADO_RECHAZADA;

                $this->nomination->update([
                    'rejected_by'   => $user_Id,
                    'rejected_at'   => now(),
                    'estado'        => $this->estado_nominacion,
                    'observaciones' => $this->observaciones,
                ]);

                AuditEvent::logEvent($this->nomination, $user_Id, AuditEvent::EVENT_APPROVAL_REJECTED, [
                    'message' => 'Nominación rechazada'
                ]);
            });
        } catch (\RuntimeException $e) {
            $this->addError('estado', $e->getMessage());
            return; // Se queda en la misma pantalla, sin redirigir
        }

        session()->flash('success', 'Nominación rechazada correctamente.');
        return redirect()->route('nominations.imprimir', $this->nomination->id);
    }
}
