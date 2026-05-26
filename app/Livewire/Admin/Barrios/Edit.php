<?php

namespace App\Livewire\Admin\Barrios;

use Livewire\Component;
use App\Models\Barrio;
use Livewire\Attributes\Layout;

#[Layout('layouts.admin')]
class Edit extends Component
{
    public Barrio $barrio;

    public string $nombre    = '';
    public string $id_DMQ    = '';
    public string $sector    = '';
    public string $parroquia = '';

    // Centroide
    public ?float $lat = null;
    public ?float $lng = null;

    // Polígono como JSON string para intercambio con el mapa JS
    public string $polygonJson = '[]';

    protected function rules(): array
    {
        return [
            'nombre'      => 'required|string|min:3|max:100',
            'id_DMQ'      => 'required|string|max:50|unique:barrios,id_DMQ,' . $this->barrio->id,
            'sector'      => 'nullable|string|max:100',
            'parroquia'   => 'nullable|string|max:255',
            'lat'         => 'nullable|numeric|between:-90,90',
            'lng'         => 'nullable|numeric|between:-180,180',
            'polygonJson' => 'nullable|string',
        ];
    }

    protected $messages = [
        'nombre.required' => 'El nombre del barrio es obligatorio.',
        'nombre.min'      => 'El nombre debe tener al menos 3 caracteres.',
        'id_DMQ.required' => 'La identificación GeoPis es obligatoria.',
        'id_DMQ.unique'   => 'Este código GeoPis ya está en uso.',
    ];

    public function mount(Barrio $barrio): void
    {
        $this->barrio    = $barrio;
        $this->nombre    = $barrio->nombre;
        $this->id_DMQ    = $barrio->id_DMQ    ?? '';
        $this->sector    = $barrio->sector    ?? '';
        $this->parroquia = $barrio->parroquia ?? '';

        // Cargar coordenadas del centroide
        if (!empty($barrio->coordenadas)) {
            $this->lat = $barrio->coordenadas['lat'] ?? null;
            $this->lng = $barrio->coordenadas['lng'] ?? null;
        }

        // Cargar polígono existente como JSON para el mapa
        $this->polygonJson = !empty($barrio->polygon)
            ? json_encode($barrio->polygon)
            : '[]';
    }

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

    public function update(): mixed
    {
        $this->validate();

        $polygon     = json_decode($this->polygonJson, true) ?: null;
        $coordenadas = ($this->lat !== null && $this->lng !== null)
            ? ['lat' => $this->lat, 'lng' => $this->lng]
            : $this->barrio->coordenadas;

        $this->barrio->update([
            'nombre'      => $this->nombre,
            'id_DMQ'      => $this->id_DMQ,
            'sector'      => $this->sector,
            'parroquia'   => $this->parroquia,
            'coordenadas' => $coordenadas,
            'polygon'     => $polygon,
        ]);

        session()->flash('message', 'Barrio actualizado correctamente.');
        return redirect()->route('barrios.index');
    }

    public function render()
    {
        return view('livewire.admin.barrios.edit');
    }
}
