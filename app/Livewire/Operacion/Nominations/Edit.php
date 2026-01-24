<?php

namespace App\Livewire\Operacion\Nominations;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Nomination;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Storage;

class Edit extends Component
{
    use WithFileUploads;

    public Nomination $nomination;

    public $users;
    public $roles;
    public $instituciones;

    public $candidate_user_id;
    public $role_name;
    public $released_by;
    public $issuer_type;
    public $pdf;
    public $fecha_emision;
    public $fecha_inicio_vigencia;
    public $fecha_fin_vigencia;
    public $observaciones;

    public function mount(Nomination $nomination)
    {
        $this->nomination = $nomination;

        $this->users = User::orderBy('last_name')->get();
        $this->roles = Role::all();


        // Cargar valores existentes
        $this->candidate_user_id = $nomination->candidate_user_id;
        $this->role_name = $nomination->role_name;
        $this->released_by = $nomination->released_by;
        $this->issuer_type = $nomination->issuer_type;
        $this->fecha_emision = $nomination->fecha_emision;
        $this->fecha_inicio_vigencia = $nomination->fecha_inicio_vigencia;
        $this->fecha_fin_vigencia = $nomination->fecha_fin_vigencia;
        $this->observaciones = $nomination->observaciones;
    }

    protected function rules()
    {
        return [
            'candidate_user_id' => 'required|exists:users,id',
            'role_name' => 'required|string',
            'released_by' => 'nullable|string',
            'issuer_type' => 'required|string',
            'pdf' => 'nullable|file|mimes:pdf|max:10240',
            'fecha_emision' => 'required|date',
            'fecha_inicio_vigencia' => 'required|date',
            'fecha_fin_vigencia' => 'required|date|after_or_equal:fecha_inicio_vigencia',
            'observaciones' => 'nullable|string|max:500',
        ];
    }

    public function save()
    {
        $this->validate();

        if ($this->pdf) {
            // Eliminar PDF anterior
            if ($this->nomination->pdf_path) {
                Storage::disk('public')->delete($this->nomination->pdf_path);
            }

            $pdfPath = $this->pdf->store('nominations', 'public');
            $this->nomination->pdf_path = $pdfPath;
        }

        $this->nomination->update([
            'candidate_user_id' => $this->candidate_user_id,
            'role_name' => $this->role_name,
            'released_by' => $this->released_by,
            'issuer_type' => $this->issuer_type,
            'fecha_emision' => $this->fecha_emision,
            'fecha_inicio_vigencia' => $this->fecha_inicio_vigencia,
            'fecha_fin_vigencia' => $this->fecha_fin_vigencia,
            'observaciones' => $this->observaciones,
        ]);

        session()->flash('success', 'La nominación fue actualizada correctamente.');
    }

    public function render()
    {
        return view('livewire.nominations.edit');
    }
}
