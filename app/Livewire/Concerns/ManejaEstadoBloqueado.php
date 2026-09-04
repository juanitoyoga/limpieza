<?php

namespace App\Livewire\Concerns;

use App\Services\LogSistemaService;
use Illuminate\Support\Facades\Auth;

trait ManejaEstadoBloqueado
{

    public bool $bloqueado = false;
    public string $bloqueadoTitulo = 'Acción no permitida';
    public string $bloqueadoMensaje = '';
    public array $bloqueadoDetalles = [];
    public string $bloqueadoRuta = '';
    public string $bloqueadoTextoBoton = 'Regresar';
    public bool $bloqueadoCerrarPestana = false;

    /**
     * Bloquea el componente y registra el intento en logs_sistema.
     *
     * @param array<string, mixed> $detalles
     */
    protected function bloquearAcceso(
        string $mensaje,
        string $ruta,
        array $detalles = [],
        string $titulo = 'Acción no permitida',
        string $textoBoton = 'Regresar',
        string $nivel = 'warning',
        bool $cerrarPestana = false,
    ): void {

        $this->bloqueado = true;
        $this->bloqueadoTitulo = $titulo;
        $this->bloqueadoMensaje = $mensaje;
        $this->bloqueadoDetalles = $detalles;
        $this->bloqueadoRuta = $ruta;
        $this->bloqueadoTextoBoton = $textoBoton;
        $this->bloqueadoCerrarPestana = $cerrarPestana;

        LogSistemaService::registrar(
            origen: static::class,
            tipoOrigen: 'livewire_bloqueo_acceso',
            nivel: $nivel,
            comentario: $titulo,
            mensajeError: [
                'mensaje'  => $mensaje,
                'detalles' => $detalles,
                'usuario'  => Auth::id(),
                'ruta'     => request()->path(),
            ],
        );
    }

    /**
     * Renderiza la vista normal o la vista de bloqueo.
     */
    protected function renderBloqueadoOr(string $viewNormal, array $data = [])
    {
        if ($this->bloqueado) {
            return view('components.estado-bloqueado', [
                'titulo'        => $this->bloqueadoTitulo,
                'mensaje'       => $this->bloqueadoMensaje,
                'detalles'      => $this->bloqueadoDetalles,
                'rutaRegreso'   => $this->bloqueadoRuta,
                'textoBoton'    => $this->bloqueadoTextoBoton,
                'cerrarPestana' => $this->bloqueadoCerrarPestana,
            ])->layout(
                'layouts.errores',
                ['titulo' => $this->bloqueadoTitulo],
            );
        }

        return view($viewNormal, $data);
    }
}
