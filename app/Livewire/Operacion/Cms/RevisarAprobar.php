<?php

namespace App\Livewire\Operacion\Cms;

use App\Models\{ContenidoVersion, AuditEvent, ContenidoCampoDefinicion};
use App\Jobs\RegistrarEventoBlockchain;
use App\Livewire\Concerns\ManejaEstadoBloqueado;
use App\Services\LogSistemaService;
use Illuminate\Support\Facades\{Auth, DB, Gate};
use Livewire\Component;
use Livewire\Attributes\{Layout, Computed, Validate};

#[Layout('layouts.operacion')]
class RevisarAprobar extends Component
{
    use ManejaEstadoBloqueado;

    public ContenidoVersion $version;

    /** Acción enviada desde el formulario: aprobar | rechazar */

    public ?string $accion = null;
    #[Validate('required|string|min:5', message: [
        'required' => 'Debe ingresar un motivo para rechazar la propuesta.',
        'min' => 'El motivo de rechazo debe tener al menos 5 caracteres.'
    ])]
    public string $motivo_rechazo = '';

    public function mount(ContenidoVersion $version)
    {
        $this->version = $version->fresh(['item.seccion', 'item.versionPublicada', 'proponente']);

        $check = Gate::inspect('cms.aprobar', $this->version);
        if (! $check->allowed()) {
            $this->bloquearAcceso(
                $check->message() ?: 'Solo un Supervisor DMQ puede aprobar cambios de contenido.',
                route('cms.lista')
            );
            return;
        }

        $estadoActual = (string) $this->version->auth_status;
        $estadoPendiente = (string) ContenidoVersion::ESTADO_PENDIENTE;

        if ($estadoActual !== $estadoPendiente) {
            $this->bloquearAcceso(
                'Esta propuesta ya fue resuelta o no está en estado pendiente.',
                route('cms.lista'),
                ['Estado actual' => $this->version->estadoLabel() ?? $estadoActual]
            );
            return;
        }
    }

    #[Computed]
    public function campos()
    {
        $seccion = $this->version->item->seccion;

        if ($seccion->relationLoaded('camposDefinicion')) {
            return $seccion->camposDefinicion;
        }

        return ContenidoCampoDefinicion::where('contenido_seccion_id', $seccion->id)
            ->where('activo', true)
            ->orderBy('orden')
            ->get();
    }

    #[Computed]
    public function versionVigente(): ?ContenidoVersion
    {
        return $this->version->item->versionPublicada;
    }

    /**
     * Método central del formulario.
     * Decide si ejecutar aprobar() o rechazar().
     */


    public function procesarAccion()
    {
        // Livewire puede enviar null, así que lo normalizamos
        $this->accion = request()->input('accion') ?? $this->accion;

        if ($this->accion === 'aprobar') {
            return $this->aprobar();
        }

        if ($this->accion === 'rechazar') {
            $this->validateOnly('motivo_rechazo');
            return $this->rechazar();
        }

        $this->addError('global', 'Acción no reconocida.');
    }


    public function aprobar()
    {
        Gate::authorize('cms.aprobar', $this->version);
        $user = Auth::user();

        try {
            $evento = DB::transaction(function () use ($user) {
                $this->version->update([
                    'auth_status'      => ContenidoVersion::ESTADO_PUBLICADA,
                    'aprobado_por'     => $user->id,
                    'fecha_aprobacion' => now(),
                ]);

                $item = $this->version->item;

                if ($item->version_publicada_id && $item->version_publicada_id !== $this->version->id) {
                    ContenidoVersion::where('id', $item->version_publicada_id)
                        ->update(['auth_status' => ContenidoVersion::ESTADO_ARCHIVADA]);
                }

                $item->update(['version_publicada_id' => $this->version->id]);

                return AuditEvent::logEvent($this->version, $user->id, 'contenido_version_publicada', [
                    'area'           => $item->seccion->area,
                    'item_id'        => $item->id,
                    'numero_version' => $this->version->numero_version,
                ]);
            });

            if ($evento) {
                DB::afterCommit(fn() => RegistrarEventoBlockchain::dispatch($evento->id));
            }
        } catch (\Throwable $e) {
            LogSistemaService::registrarExcepcion(static::class, 'cms_aprobar', $e);
            $this->addError('global', 'Error al aprobar y publicar el contenido: ' . $e->getMessage());
            return;
        }

        session()->flash('message', 'Contenido aprobado y publicado.');
        return redirect()->route('cms.lista');
    }

    public function rechazar()
    {
        Gate::authorize('cms.aprobar', $this->version);
        $this->validateOnly('motivo_rechazo');
        $user = Auth::user();

        try {
            $evento = DB::transaction(function () use ($user) {
                $this->version->update([
                    'auth_status'    => ContenidoVersion::ESTADO_RECHAZADA,
                    'rechazado_por'  => $user->id,
                    'fecha_rechazo'  => now(),
                    'motivo_rechazo' => $this->motivo_rechazo,
                ]);

                return AuditEvent::logEvent($this->version, $user->id, 'contenido_version_rechazada', [
                    'motivo' => $this->motivo_rechazo,
                ]);
            });

            if ($evento) {
                DB::afterCommit(fn() => RegistrarEventoBlockchain::dispatch($evento->id));
            }
        } catch (\Throwable $e) {
            LogSistemaService::registrarExcepcion(static::class, 'cms_rechazar', $e);
            $this->addError('global', 'Error al rechazar la propuesta: ' . $e->getMessage());
            return;
        }

        session()->flash('message', 'Propuesta rechazada.');
        return redirect()->route('cms.lista');
    }

    public function render()
    {
        return $this->renderBloqueadoOr('livewire.operacion.cms.revisar-aprobar');
    }
}
