<?php

namespace App\Livewire\Operacion\Proveedores;

use App\Models\Proveedor;
use App\Models\Contacto;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.operacion')]
class Show extends Component
{
    public Proveedor $proveedor;

    // Estado del modal de contacto (crear/editar)
    public bool $showContactoModal = false;
    public ?int $contactoId = null;
    public $nombre;
    public $cargo;
    public $telefono;
    public $email;
    public $es_principal = false;

    // Estado del modal de confirmación de borrado
    public bool $confirmingDelete = false;
    public ?int $deleteId = null;

    protected function rules(): array
    {
        return [
            'nombre'       => 'required|string|max:255',
            'cargo'        => 'nullable|string|max:255',
            'telefono'     => 'nullable|string|max:20',
            'email'        => 'nullable|email|max:255',
            'es_principal' => 'boolean',
        ];
    }

    public function mount(Proveedor $proveedor)
    {
        $this->proveedor = $proveedor;
    }

    public function openCreateContacto(): void
    {
        $this->resetContactoForm();
        $this->showContactoModal = true;
    }

    public function openEditContacto(int $contactoId): void
    {
        $contacto = $this->proveedor->contactos()->findOrFail($contactoId);

        $this->contactoId   = $contacto->id;
        $this->nombre       = $contacto->nombre;
        $this->cargo        = $contacto->cargo;
        $this->telefono     = $contacto->telefono;
        $this->email        = $contacto->email;
        $this->es_principal = $contacto->es_principal;

        $this->showContactoModal = true;
    }

    public function saveContacto(): void
    {
        $this->validate();

        // Si este contacto se marca como principal, desmarca a los demás
        if ($this->es_principal) {
            $this->proveedor->contactos()
                ->where('id', '!=', $this->contactoId)
                ->update(['es_principal' => false]);
        }

        $this->proveedor->contactos()->updateOrCreate(
            [
                'id'            => $this->contactoId,
                'nombre'       => $this->nombre,
                'cargo'        => $this->cargo,
                'telefono'     => $this->telefono,
                'email'        => $this->email,
                'es_principal' => $this->es_principal,
            ]
        );

        $this->showContactoModal = false;
        $this->resetContactoForm();

        session()->flash('message', 'Contacto guardado correctamente.');
    }

    public function confirmDelete(int $id): void
    {
        $this->deleteId = $id;
        $this->confirmingDelete = true;
    }

    public function deleteContacto(): void
    {
        $this->proveedor->contactos()->findOrFail($this->deleteId)->delete();

        $this->confirmingDelete = false;
        $this->deleteId = null;

        session()->flash('message', 'Contacto eliminado correctamente.');
    }

    private function resetContactoForm(): void
    {
        $this->contactoId   = null;
        $this->nombre       = null;
        $this->cargo        = null;
        $this->telefono     = null;
        $this->email        = null;
        $this->es_principal = false;
        $this->resetErrorBag();
    }

    public function render()
    {
        return view('livewire.operacion.proveedores.show', [
            'contactos' => $this->proveedor->contactos()->orderByDesc('es_principal')->orderBy('nombre')->get(),
        ]);
    }
}
