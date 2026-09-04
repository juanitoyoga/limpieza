<?php

namespace App\Livewire\Admin;

use App\Models\User;
use App\Services\LogSistemaService;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;

/**
 * Protege contra el escenario de "teléfono perdido/robado con sesión
 * activa": la expiración deslizante (ver ExtendTokenExpiration) solo
 * cierra sesiones abandonadas, no un uso fraudulento activo. Esta
 * pantalla le da al Admin el botón para cortar eso manualmente.
 *
 * Uso: <livewire:admin.sesiones-usuario :user-id="$user->id" />
 * o como página completa vía ruta con {user} en el path.
 */
class SesionesUsuario extends Component
{
    public int $userId;

    public ?User $usuario = null;

    public ?int $tokenIdAConfirmar = null;
    public bool $confirmandoRevocarTodas = false;

    public function mount(int $userId): void
    {
        abort_unless(Gate::allows('gestionar-sesiones'), 403);

        $this->userId = $userId;
        $this->usuario = User::findOrFail($userId);
    }

    public function getTokensProperty()
    {
        return $this->usuario->tokens()
            ->orderByDesc('last_used_at')
            ->get();
    }

    public function confirmarRevocar(int $tokenId): void
    {
        $this->tokenIdAConfirmar = $tokenId;
    }

    public function cancelarConfirmacion(): void
    {
        $this->tokenIdAConfirmar = null;
        $this->confirmandoRevocarTodas = false;
    }

    public function revocar(int $tokenId): void
    {
        abort_unless(Gate::allows('gestionar-sesiones'), 403);

        $token = $this->usuario->tokens()->where('id', $tokenId)->first();

        if ($token) {
            $token->delete();

            LogSistemaService::registrar(
                origen: 'SesionesUsuario',
                tipoOrigen: 'LIVEWIRE',
                nivel: 'WARNING',
                comentario: "Token #{$tokenId} revocado manualmente para user #{$this->usuario->id} por admin #" . auth()->id(),
            );
        }

        $this->tokenIdAConfirmar = null;
        $this->dispatch('sesion-revocada');
    }

    public function confirmarRevocarTodas(): void
    {
        $this->confirmandoRevocarTodas = true;
    }

    public function revocarTodas(): void
    {
        abort_unless(Gate::allows('gestionar-sesiones'), 403);

        $count = $this->usuario->tokens()->count();
        $this->usuario->tokens()->delete();

        LogSistemaService::registrar(
            origen: 'SesionesUsuario',
            tipoOrigen: 'LIVEWIRE',
            nivel: 'WARNING',
            comentario: "{$count} tokens revocados para user #{$this->usuario->id} por admin #" . auth()->id(),
        );

        $this->confirmandoRevocarTodas = false;
        $this->dispatch('sesion-revocada');
    }

    public function render()
    {
        return view('livewire.admin.sesiones-usuario', [
            'tokens' => $this->tokens,
        ]);
    }
}
