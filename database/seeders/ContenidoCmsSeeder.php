<?php

namespace Database\Seeders;

use App\Models\{ContenidoSeccion, ContenidoCampoDefinicion, ContenidoItem};
use Illuminate\Database\Seeder;

class ContenidoCmsSeeder extends Seeder
{
    public function run(): void
    {
        // 1) Especificaciones — UNA fila por zona de la página.
        $especificaciones = [
            'banner' => [ // slides del carrousel — 1900x1200, 5 posiciones fijas
                'multiplicidad' => 'coleccion_limitada',
                'max_items'     => 5,
                'descripcion'   => 'Carrousel principal del home (slides)',
                'campos' => [
                    ['clave' => 'imagen_principal', 'etiqueta' => 'Imagen de fondo',   'tipo_dato' => 'imagen',      'requerido' => true,  'imagen_ancho' => 1900, 'imagen_alto' => 1200, 'orden' => 1],
                    ['clave' => 'subtitulo',        'etiqueta' => 'Subtítulo',         'tipo_dato' => 'texto',       'requerido' => false, 'orden' => 2],
                    ['clave' => 'titulo',           'etiqueta' => 'Título',            'tipo_dato' => 'texto',       'requerido' => true,  'orden' => 3],
                    ['clave' => 'texto_largo',      'etiqueta' => 'Texto descriptivo', 'tipo_dato' => 'texto_largo', 'requerido' => false, 'orden' => 4],
                    ['clave' => 'texto_boton',      'etiqueta' => 'Texto del botón',   'tipo_dato' => 'texto',       'requerido' => false, 'orden' => 5],
                    ['clave' => 'url_destino',      'etiqueta' => 'Enlace del botón',  'tipo_dato' => 'url', 'url_externa_obligatoria' => false, 'requerido' => false, 'orden' => 6],
                ],
            ],

            'noticia' => [ // colección libre — foto 270x340
                'multiplicidad' => 'coleccion_libre',
                'descripcion'   => 'Sección de noticias (sliding infinito)',
                'campos' => [
                    ['clave' => 'imagen_principal',  'etiqueta' => 'Imagen',                 'tipo_dato' => 'imagen',      'requerido' => true,  'imagen_ancho' => 270, 'imagen_alto' => 340, 'orden' => 1],
                    ['clave' => 'titulo',            'etiqueta' => 'Título',                 'tipo_dato' => 'texto',       'requerido' => true,  'orden' => 2],
                    ['clave' => 'texto_largo',       'etiqueta' => 'Cuerpo',                 'tipo_dato' => 'texto_largo', 'requerido' => false, 'orden' => 3],
                    ['clave' => 'url_destino',       'etiqueta' => 'Fuente / blog',          'tipo_dato' => 'url', 'url_externa_obligatoria' => false, 'requerido' => false, 'orden' => 4],
                    ['clave' => 'documento_adjunto', 'etiqueta' => 'PDF adjunto (opcional)', 'tipo_dato' => 'documento_pdf', 'requerido' => false, 'orden' => 5],
                ],
            ],

            'banner_publicitario' => [ // "auspiciantes" — colección libre, 750x450, enlace externo obligatorio
                'multiplicidad' => 'coleccion_libre',
                'descripcion'   => 'Banners publicitarios de auspiciantes',
                'campos' => [
                    ['clave' => 'imagen_principal', 'etiqueta' => 'Imagen publicitaria', 'tipo_dato' => 'imagen', 'requerido' => true, 'imagen_ancho' => 750, 'imagen_alto' => 450, 'orden' => 1],
                    ['clave' => 'url_destino',      'etiqueta' => 'Enlace externo',      'tipo_dato' => 'url', 'url_externa_obligatoria' => true, 'requerido' => true, 'orden' => 2],
                ],
            ],

            'auspiciador' => [ // "logos" — colección libre, 240x120, enlace externo obligatorio
                'multiplicidad' => 'coleccion_libre',
                'descripcion'   => 'Logos de auspiciantes/medios con enlace',
                'campos' => [
                    ['clave' => 'imagen_principal', 'etiqueta' => 'Logo',      'tipo_dato' => 'imagen', 'requerido' => true, 'imagen_ancho' => 240, 'imagen_alto' => 120, 'orden' => 1],
                    ['clave' => 'titulo',           'etiqueta' => 'Nombre',    'tipo_dato' => 'texto',  'requerido' => true, 'orden' => 2],
                    ['clave' => 'url_destino',      'etiqueta' => 'Sitio web', 'tipo_dato' => 'url', 'url_externa_obligatoria' => true, 'requerido' => true, 'orden' => 3],
                ],
            ],

            'mejor_barrio' => [ // colección libre, 300x600, orden curado manualmente
                'multiplicidad' => 'coleccion_libre',
                'descripcion'   => 'Ranking de mejores barrios',
                'campos' => [
                    ['clave' => 'imagen_principal', 'etiqueta' => 'Foto',              'tipo_dato' => 'imagen', 'requerido' => true, 'imagen_ancho' => 300, 'imagen_alto' => 600, 'orden' => 1],
                    ['clave' => 'titulo',           'etiqueta' => 'Nombre del barrio', 'tipo_dato' => 'texto',  'requerido' => true, 'orden' => 2],
                ],
            ],
        ];

        foreach ($especificaciones as $area => $spec) {
            $seccion = ContenidoSeccion::updateOrCreate(
                ['area' => $area, 'version_spec' => 1], // primera especificación de cada área
                [
                    'activo'        => true,
                    'multiplicidad' => $spec['multiplicidad'],
                    'max_items'     => $spec['max_items'] ?? null,
                    'plataforma'    => 'web',
                    'descripcion'   => $spec['descripcion'],
                ]
            );

            foreach ($spec['campos'] as $campo) {
                ContenidoCampoDefinicion::updateOrCreate(
                    ['contenido_seccion_id' => $seccion->id, 'clave' => $campo['clave']],
                    $campo
                );
            }
        }

        // 2) Items — SOLO se pre-crean los del carrousel (slots fijos).
        // Los de colección libre (noticia, banner_publicitario,
        // auspiciador, mejor_barrio) nacen cuando alguien los propone
        // por primera vez, vía Proponer.php.
        $seccionBanner = ContenidoSeccion::activaPara('banner');

        foreach (range(1, 5) as $posicion) {
            ContenidoItem::firstOrCreate(
                ['contenido_seccion_id' => $seccionBanner->id, 'identificador' => "slide_{$posicion}"],
                ['orden' => $posicion, 'activo' => true]
            );
        }
    }
}
