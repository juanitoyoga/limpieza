<?php

namespace App\Livewire\Operacion\Proveedores;

use App\Models\Proveedor;
use App\Models\Contacto;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.operacion')]
class Show extends Component
{
    public Proveedor $proveedor;

    // Estado del modal de contacto (crear/editar)
    public bool $showContactoModal = false;
    public ?int $contactoId = null;
    public $first_name;
    public $last_name;
    public $cargo;
    public $phone;
    public $email;
    public $es_principal = false;

    // 🆕 Acceso a la app móvil (genera User + Contratista automáticamente
    // vía ContactoObserver cuando se marca)
    public bool $usa_app = false;
    public $tipo_id = 'CEDULA';
    public $nro_id;
    public bool $yaGenerado = false; // true si este contacto ya tiene Contratista

    // Estado del modal de confirmación de borrado
    public bool $confirmingDelete = false;
    public ?int $deleteId = null;

    protected function rules(): array
    {
        // Si este contacto ya generó cuenta (edición), lo excluimos de
        // las validaciones unique para no chocar contra sí mismo.
        $userIdExcluir = $this->contactoId
            ? Contacto::find($this->contactoId)?->contratista?->user_id
            : null;

        return [
            'first_name'   => 'required|string|max:255',
            'last_name'    => 'nullable|string|max:255',
            'cargo'        => 'nullable|string|max:255',
            'phone'        => 'nullable|string|max:20',
            'email'        => [
                Rule::requiredIf($this->usa_app),
                'nullable',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($userIdExcluir),
            ],
            'es_principal' => 'boolean',
            'usa_app'      => 'boolean',
            'tipo_id'      => [Rule::requiredIf($this->usa_app), 'nullable', 'string', 'max:20'],
            'nro_id'       => [
                Rule::requiredIf($this->usa_app),
                'nullable',
                'string',
                'max:20',
                Rule::unique('users', 'nro_id')->ignore($userIdExcluir),
            ],
        ];
    }

    protected $messages = [
        'email.required_if'  => 'El email es obligatorio para generar acceso a la app.',
        'email.unique'       => 'Ya existe un usuario con este email en el sistema.',
        'nro_id.required_if' => 'La identificación es obligatoria para generar acceso a la app.',
        'nro_id.unique'      => 'Ya existe un usuario con esta identificación en el sistema.',
    ];

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
        $this->first_name   = $contacto->first_name;
        $this->last_name    = $contacto->last_name;
        $this->cargo        = $contacto->cargo;
        $this->phone        = $contacto->phone;
        $this->email        = $contacto->email;
        $this->es_principal = $contacto->es_principal;
        $this->usa_app      = $contacto->usa_app;
        $this->tipo_id      = $contacto->tipo_id ?? 'CEDULA';
        $this->nro_id       = $contacto->nro_id;
        $this->yaGenerado   = $contacto->contratista()->exists();

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
            ['id' => $this->contactoId],
            [
                'first_name'   => $this->first_name,
                'last_name'    => $this->last_name,
                'cargo'        => $this->cargo,
                'phone'        => $this->phone,
                'email'        => $this->email,
                'es_principal' => $this->es_principal,
                'usa_app'      => $this->usa_app,
                'tipo_id'      => $this->tipo_id,
                'nro_id'       => $this->nro_id,
                'is_active'    => true,
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
        $this->first_name   = null;
        $this->last_name    = null;
        $this->cargo        = null;
        $this->phone        = null;
        $this->email        = null;
        $this->es_principal = false;
        $this->usa_app      = false;
        $this->tipo_id      = 'CEDULA';
        $this->nro_id       = null;
        $this->yaGenerado   = false;
        $this->resetErrorBag();
    }

    public function render()
    {
        return view('livewire.operacion.proveedores.show', [
            'contactos' => $this->proveedor->contactos()->orderByDesc('es_principal')->orderBy('first_name')->get(),
        ]);
    }
}
