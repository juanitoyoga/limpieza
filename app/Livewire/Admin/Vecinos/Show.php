<?php

namespace App\Livewire\Admin\Vecinos;

use App\Models\User;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.admin')]
class Show extends Component
{
    public User $user;

    // Cambiamos el parámetro a $vecino para que coincida exactamente con el {vecino} de tu ruta
    public function mount($vecino)
    {
        // 1. Buscamos al usuario usando el ID recibido (que viene en la variable $vecino)
        $usuarioEncontrado = User::with(['vecino.barrio'])->find($vecino);

        // 2. Validamos si el usuario existe y si tiene el perfil de vecino
        if (!$usuarioEncontrado || !$usuarioEncontrado->vecino) {
            return redirect()->route('vecinos.lista')
                ->with('message', 'Este usuario no tiene registro de vecino o no existe.');
        }

        // 3. Asignamos el usuario encontrado a la propiedad pública
        $this->user = $usuarioEncontrado;
    }

    public function render()
    {
        return view('livewire.admin.vecinos.show', [
            'user'   => $this->user,
            'vecino' => $this->user->vecino,
            'barrio' => $this->user->vecino->barrio,
        ]);
    }
}
