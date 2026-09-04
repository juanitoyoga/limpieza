<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\VisualSlot;

class VisualSlotSeeder extends Seeder
{
    public function run(): void
    {
        $slots = [
            [
                'key' => 'carrusel_principal',
                'label' => 'Carrusel Principal Home',
                'description' => 'Slider destacado en la parte superior de la web y app',
                'scope' => 'global',
                'allowed_fields' => ['titulo','descripcion','imagen','orden','link_url'],
                'max_items' => 5,
            ],
            [
                'key' => 'noticias',
                'label' => 'Noticias DMQ / Barriales',
                'description' => 'Noticias con editor enriquecido',
                'scope' => 'por_barrio',
                'allowed_fields' => ['titulo','resumen','cuerpo_html','imagen_destacada','categoria','fecha_publicacion'],
                'max_items' => 50,
            ],
            [
                'key' => 'galeria_fotos',
                'label' => 'Galería de Fotos',
                'description' => 'Álbumes de mingas y eventos',
                'scope' => 'por_barrio',
                'allowed_fields' => ['album_nombre','descripcion','imagenes_multiples'],
                'max_items' => 100,
            ],
            [
                'key' => 'logos_auspiciantes',
                'label' => 'Logos Auspiciantes',
                'description' => 'Logos de colaboradores',
                'scope' => 'global',
                'allowed_fields' => ['nombre','imagen','link_url'],
                'max_items' => 20,
            ],
            [
                'key' => 'banners_comerciales',
                'label' => 'Banners Publicitarios',
                'description' => 'Banners con vigencia',
                'scope' => 'global',
                'allowed_fields' => ['titulo','imagen','texto','link_url','fecha_inicio','fecha_fin'],
                'max_items' => 6,
            ],
        ];

        foreach($slots as $slot){ VisualSlot::updateOrCreate(['key'=>$slot['key']], $slot); }
    }
}
