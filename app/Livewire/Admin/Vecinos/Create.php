<?php

namespace App\Livewire\Admin\Vecinos;

use App\Models\Vecino;
use App\Models\Barrio;
use App\Models\Catalogo;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\DB;

#[Layout('layouts.admin')]
class Create extends Component
{
    // Datos de ubicación seleccionados mediante el mapa web
    public ?int $barrio_id = null;
    public string $calle_principal = '';
    public string $calle_secundaria = '';
    public string $numero = '';
    public string $referencias = '';
    public string $telefono = '';

    // Coordenadas obtenidas del Pin del mapa en la interfaz web
    public ?float $latitud = null;
    public ?float $longitud = null;

    // Catálogos adicionales
    public $barrios = [];
    public array $ocupaciones = [];
    public array $deportes = [];
    public array $recreaciones = [];

    public $catalogoOcupaciones = [];
    public $catalogoDeportes = [];
    public $catalogoRecreaciones = [];

    public function mount()
    {
        // 1. Bloqueo de seguridad: Validar si ya tiene un perfil operativo
        if (in_array(auth()->user()?->role_name, ['Vecino', 'Dirigente', 'Presidente', 'Funcionario'])) {
            return redirect()->route('dashboard')
                ->with('error', 'Tu cuenta ya cuenta con un perfil verificado en el sistema.');
        }

        $this->barrios = Barrio::orderBy('nombre')->get();
        $this->catalogoOcupaciones = Catalogo::where('tipo', 'ocupacion')->orderBy('nombre')->get();
        $this->catalogoDeportes    = Catalogo::where('tipo', 'deporte')->orderBy('nombre')->get();
        $this->catalogoRecreaciones = Catalogo::where('tipo', 'recreacion')->orderBy('nombre')->get();
    }

    public function save()
    {
        $this->validate([
            'barrio_id'        => 'required|exists:barrios,id',
            'calle_principal'  => 'required|string|max:255',
            'calle_secundaria' => 'required|string|max:255',
            'numero'           => 'required|string|max:50',
            'latitud'          => 'required|numeric',
            'longitud'         => 'required|numeric',
            'telefono'         => 'nullable|string|max:20',
            'referencias'      => 'nullable|string|max:500',
            'ocupaciones'      => 'array',
            'deportes'         => 'array',
            'recreaciones'     => 'array',
        ], [
            'barrio_id.required'       => 'Por favor, selecciona tu barrio.',
            'latitud.required'         => 'Debes marcar la ubicación de tu casa en el mapa.',
            'calle_principal.required' => 'La calle principal es obligatoria.',
        ]);

        $barrio = Barrio::findOrFail($this->barrio_id);

        // 2. Ejecutar Algoritmo Ray-Casting sobre el JSON del Polígono
        $dentroDePoligono = $this->validarCoordenadasEnPoligono($this->latitud, $this->longitud, $barrio);

        if (!$dentroDePoligono) {
            $this->addError('barrio_id', 'La ubicación de tu domicilio se encuentra fuera del límite perimetral de este barrio.');
            return;
        }

        try {
            DB::transaction(function () use ($barrio) {
                // 3. Crear el registro extendido del Vecino vinculado al User logueado
                Vecino::create([
                    'user_id'          => auth()->id(),
                    // 'barrio_id'        => $barrio->id,
                    'id_DMQ'           => $barrio->id_DMQ,
                    'cedula'           => auth()->user()->nro_id, // Heredado del registro base
                    'telefono'         => $this->telefono ?: auth()->user()->phone,
                    'calle_principal'  => $this->calle_principal,
                    'calle_secundaria' => $this->calle_secundaria,
                    'numero'           => $this->numero,
                    'referencias'      => $this->referencias,
                    'fecha_registro'   => now(),
                    'is_active'        => true,
                    'ocupacion'        => $this->ocupaciones,
                    'deportes'         => $this->deportes,
                    'recreacion'       => $this->recreaciones,
                ]);

                // 4. Ascender el rol global de la sesión
                auth()->user()->update([
                    'role_name' => 'Vecino'
                ]);
            });

            session()->flash('message', '¡Perfil verificado! Ahora eres formalmente Vecino de tu rincón.');
            return redirect()->route('dashboard');
        } catch (\Exception $e) {
            $this->addError('server', 'Error al procesar la verificación: ' . $e->getMessage());
        }
    }

    private function validarCoordenadasEnPoligono($lat, $lng, $barrio): bool
    {
        $vertices = $barrio->polygon;
        if (empty($vertices) || !is_array($vertices)) return false;

        if (isset($vertices['coordinates'])) {
            $vertices = $vertices['coordinates'][0];
        }

        $numVertices = count($vertices);
        $dentro = false;
        $j = $numVertices - 1;

        for ($i = 0; $i < $numVertices; $i++) {
            $vertexI_lat = isset($vertices[$i]['lat']) ? (float)$vertices[$i]['lat'] : (float)$vertices[$i][1];
            $vertexI_lng = isset($vertices[$i]['lng']) ? (float)$vertices[$i]['lng'] : (float)$vertices[$i][0];
            $vertexJ_lat = isset($vertices[$j]['lat']) ? (float)$vertices[$j]['lat'] : (float)$vertices[$j][1];
            $vertexJ_lng = isset($vertices[$j]['lng']) ? (float)$vertices[$j]['lng'] : (float)$vertices[$j][0];

            if ((($vertexI_lng > $lng) != ($vertexJ_lng > $lng)) &&
                ($lat < ($vertexJ_lat - $vertexI_lat) * ($lng - $vertexI_lng) / ($vertexJ_lng - $vertexI_lng + 0.000000001) + $vertexI_lat)
            ) {
                $dentro = !$dentro;
            }
            $j = $i;
        }

        return $dentro;
    }

    public function render()
    {
        return view('livewire.admin.vecinos.create');
    }
}
