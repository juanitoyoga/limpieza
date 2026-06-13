<?php

namespace App\Livewire\Vecinos;

use App\Models\Barrio;
use App\Models\Vecino;
use App\Models\UserRole;
use App\Services\GeoService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class Registro extends Component
{
    // Campos del formulario
    public string  $cedula           = '';
    public string  $telefono         = '';
    public string  $id_DMQ           = '';
    public string  $calle_principal  = '';
    public string  $numero           = '';
    public string  $calle_secundaria = '';
    public string  $referencias      = '';
    public float   $latitud          = 0;
    public float   $longitud         = 0;

    // Listas de opciones (checkboxes)
    public array $ocupacion  = [];
    public array $deportes   = [];
    public array $recreacion = [];

    // Opciones disponibles
    public array $ocupacionOpciones = [
        'Comerciante',
        'Docente',
        'Médico',
        'Ingeniero',
        'Abogado',
        'Estudiante',
        'Ama de casa',
        'Obrero',
        'Empleado público',
        'Otro',
    ];
    public array $deportesOpciones = [
        'Fútbol',
        'Básquet',
        'Natación',
        'Ciclismo',
        'Atletismo',
        'Voleibol',
        'Tenis',
        'Artes marciales',
        'Otro',
    ];
    public array $recreacionOpciones = [
        'Lectura',
        'Cine',
        'Música',
        'Viajes',
        'Cocina',
        'Jardinería',
        'Videojuegos',
        'Teatro',
        'Otro',
    ];

    // Estado de validación geográfica
    public bool    $ubicacionValida  = false;
    public ?string $barrioNombre     = null;
    public ?string $barrioError      = null;

    public function mount(): void
    {
        // Si ya es vecino activo, redirigir al perfil
        $user = Auth::user();
        if ($user->vecino && $user->vecino->is_active) {
            $this->redirect(route('vecino.perfil'));
        }

        // Pre-cargar cédula del usuario si la tiene
        $this->cedula = $user->cedula ?? '';
    }

    public function validarUbicacion(): void
    {
        $this->validate([
            'latitud'  => 'required|numeric|between:-90,90',
            'longitud' => 'required|numeric|between:-180,180',
            'id_DMQ'   => 'required|string',
        ]);

        $barrio = Barrio::where('id_DMQ', $this->id_DMQ)->where('activo', true)->first();

        if (! $barrio) {
            $this->barrioError     = 'El barrio seleccionado no existe o está inactivo.';
            $this->ubicacionValida = false;
            return;
        }

        if (empty($barrio->polygon)) {
            $this->barrioError     = 'El barrio no tiene polígono registrado. Contacta al administrador.';
            $this->ubicacionValida = false;
            return;
        }

        $geoService = app(GeoService::class);
        $dentro     = $geoService->pointInPolygon(
            lat: $this->latitud,
            lng: $this->longitud,
            polygon: $barrio->polygon,
        );

        if (! $dentro) {
            $this->barrioError     = 'Tu ubicación GPS está fuera del polígono del barrio seleccionado.';
            $this->ubicacionValida = false;
            return;
        }

        $this->ubicacionValida = true;
        $this->barrioNombre    = $barrio->nombre_completo;
        $this->barrioError     = null;
    }

    public function registrar(): void
    {
        $this->validate([
            'cedula'           => 'required|string|size:10|unique:vecinos,cedula',
            'telefono'         => 'nullable|string|max:15',
            'id_DMQ'           => 'required|string|exists:barrios,id_DMQ',
            'calle_principal'  => 'required|string|max:150',
            'numero'           => 'required|string|max:20',
            'calle_secundaria' => 'required|string|max:150',
            'referencias'      => 'nullable|string|max:300',
            'latitud'          => 'required|numeric',
            'longitud'         => 'required|numeric',
        ]);

        if (! $this->ubicacionValida) {
            $this->addError('ubicacion', 'Debes validar tu ubicación antes de registrarte.');
            return;
        }

        $user = Auth::user();

        DB::transaction(function () use ($user) {
            // Obtener o crear el UserRole para vecino
            $userRole = UserRole::firstOrCreate(
                ['user_id' => $user->id, 'role_name' => 'Vecino'],
                ['assigned_at' => now()]
            );

            Vecino::create([
                'userroles_id'    => $userRole->id,
                'user_id'         => $user->id,
                'id_DMQ'          => $this->id_DMQ,
                'cedula'          => $this->cedula,
                'telefono'        => $this->telefono ?: null,
                'fecha_registro'  => now()->toDateString(),
                'calle_principal' => $this->calle_principal,
                'numero'          => $this->numero,
                'calle_secundaria' => $this->calle_secundaria,
                'referencias'     => $this->referencias ?: null,
                'ocupacion'       => $this->ocupacion  ?: null,
                'deportes'        => $this->deportes   ?: null,
                'recreacion'      => $this->recreacion ?: null,
                'is_active'       => true,
            ]);
        });

        session()->flash('success', '¡Te has registrado exitosamente como vecino!');
        $this->redirect(route('vecino.perfil'));
    }

    public function render()
    {
        $barrios = Barrio::activos()->orderBy('nombre')->get(['id_DMQ', 'nombre', 'parroquia', 'sector']);
        return view('livewire.vecino.registro', compact('barrios'));
    }
}
