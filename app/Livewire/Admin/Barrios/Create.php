<?php

namespace App\Livewire\Admin\Barrios;

use Livewire\Component;
use App\Models\Barrio;
use Livewire\Attributes\Layout;

#[Layout('layouts.admin')]
class Create extends Component
{
    public string $nombre      = '';
    public string $id_DMQ      = '';
    public string $sector      = '';
    public string $parroquia   = '';

    // Centroide — marcador en el mapa
    public ?float $lat = null;
    public ?float $lng = null;

    // Polígono — JSON string desde el mapa: [{lat,lng},{lat,lng},...]
    public string $polygonJson = '[]';

    protected $rules = [
        'nombre'      => 'required|string|min:3|max:100',
        'id_DMQ'      => 'required|string|max:50|unique:barrios,id_DMQ',
        'sector'      => 'required|string|max:100',
        'parroquia'   => 'required|string|max:255',
        'lat'         => 'nullable|numeric|between:-90,90',
        'lng'         => 'nullable|numeric|between:-180,180',
        'polygonJson' => 'nullable|string',
    ];

    protected $messages = [
        'nombre.required'    => 'El nombre del barrio es obligatorio.',
        'nombre.min'         => 'El nombre debe tener al menos 3 caracteres.',
        'id_DMQ.required'    => 'La identificación GeoPis es obligatoria.',
        'id_DMQ.unique'      => 'Este código GeoPis ya está registrado.',
        'sector.required'    => 'El sector es obligatorio.',
        'parroquia.required' => 'La parroquia es obligatoria.',
    ];

    // Llamado desde JS cuando el usuario mueve el marcador de centroide
    public function updateCoordenadas(float $lat, float $lng): void
    {
        $this->lat = $lat;
        $this->lng = $lng;
    }

    // Llamado desde JS cuando el usuario dibuja/modifica el polígono
    public function updatePolygon(string $polygonJson): void
    {
        $this->polygonJson = $polygonJson;
    }

    public function store(): mixed
    {
        $this->validate();

        $polygon     = json_decode($this->polygonJson, true) ?: null;
        $coordenadas = ($this->lat !== null && $this->lng !== null)
            ? ['lat' => $this->lat, 'lng' => $this->lng]
            : null;

        Barrio::create([
            'nombre'      => $this->nombre,
            'id_DMQ'      => $this->id_DMQ,
            'sector'      => $this->sector,
            'parroquia'   => $this->parroquia,
            'coordenadas' => $coordenadas,
            'polygon'     => $polygon,
            'activo'      => true,
        ]);

        session()->flash('message', 'Barrio creado correctamente.');
        return redirect()->route('barrios.index');
    }
    public $readyToLoad = false;

    public function initComponent()
    {
        $this->readyToLoad = true;
    }
    public function render()
    {
        return view('livewire.admin.barrios.create');
    }
}
