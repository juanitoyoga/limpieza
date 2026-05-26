<?php


namespace App\Actions;

use App\Models\Catalogo;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SyncCatalogoAction
{
    public function execute($priorizarEcuador = true)
    {
        $nuevos = 0;

        // 1. Sincronización masiva
        $nuevos += $this->fetchMassiveData();

        // 2. Elementos locales (Ecuador/Hispanoamérica)
        if ($priorizarEcuador) {
            $idsLocales = [
                'Q283350',   // Ecuavóley
                'Q6067313',  // Pelota Nacional
                'Q115682845', // Reciclador
                'Q12411690', // Comerciante
                'Q5099308',  // Chiva
            ];

            foreach ($idsLocales as $id) {
                if ($item = $this->fetchSpecificEntity($id)) {
                    $this->storeEntity($id, $item['label'], $item['tipo_sugerido'], true);
                    $nuevos++;
                }
            }
        }

        return $nuevos;
    }

    private function fetchMassiveData()
    {
        $nuevos = 0;
/* --
        $queries = [ 
            'deporte'       => 'SELECT ?item ?itemLabel WHERE { ?item wdt:P31 wd:Q31629. 
                                SERVICE wikibase:label { bd:serviceParam wikibase:language "es". } }', 
            'ocupacion'     => 'SELECT ?item ?itemLabel WHERE { ?item wdt:P31 wd:Q12737077. 
                                SERVICE wikibase:label { bd:serviceParam wikibase:language "es". } }', 
            'recreacion'    => 'SELECT ?item ?itemLabel WHERE { ?item wdt:P31 wd:Q205544. 
                                SERVICE wikibase:label { bd:serviceParam wikibase:language "es". } }', 
        ];
-- */
        $queries = [ 
            // Q31629: Deporte. Filtramos que la etiqueta sea estrictamente 'es'
            'deporte' => 'SELECT DISTINCT ?item ?itemLabel WHERE { 
                            ?item wdt:P31 wd:Q31629. 
                            ?item rdfs:label ?itemLabel. 
                            FILTER(LANG(?itemLabel) = "es") 
                         }', 
        
            // Q12737077: Ocupación. Se añade el filtro de idioma para evitar nombres en otros idiomas
            'ocupacion' => 'SELECT DISTINCT ?item ?itemLabel WHERE { 
                                ?item wdt:P31 wd:Q12737077. 
                                ?item rdfs:label ?itemLabel. 
                                FILTER(LANG(?itemLabel) = "es") 
                            }', 
        
            'recreacion' => 'SELECT DISTINCT ?item ?itemLabel WHERE {
                VALUES ?tipo {
                wd:Q136962    # pasatiempo
                wd:Q205544    # juego
                wd:Q28648     # deporte
                wd:Q188784    # actividad recreativa
                wd:Q1914636   # actividad de ocio
                }
            
                ?item wdt:P31 ?tipo.
                ?item rdfs:label ?itemLabel.
                FILTER(LANG(?itemLabel) = "es")
            }',
          
        ];        

        foreach ($queries as $tipo => $sparql) {
            $response = Http::get('https://query.wikidata.org/sparql', [
                'query' => $sparql,
                'format' => 'json'
            ]);

            if ($response->successful()) {
                foreach ($response->json()['results']['bindings'] as $row) {
                    $id = str_replace('http://www.wikidata.org/entity/', '', $row['item']['value']);
                    $label = $row['itemLabel']['value'] ?? 'Sin nombre';
                    $this->storeEntity($id, $label, $tipo);
                    $nuevos++;
                }
            } else {
                Log::error("Error en SPARQL $tipo: ".$response->body());
            }
        }

        return $nuevos;
    }

    private function fetchSpecificEntity($id)
    {
        $response = Http::get("https://www.wikidata.org/w/api.php", [
            'action' => 'wbgetentities',
            'ids' => $id,
            'languages' => 'es',
            'format' => 'json'
        ]);

        if ($response->successful()) {
            $entity = $response->json()['entities'][$id];
            return [
                'label' => $entity['labels']['es']['value'] ?? 'Sin nombre',
                'tipo_sugerido' => $this->inferirTipo($id)
            ];
        }
        return null;
    }

    private function inferirTipo($id)
    {
        $mapa = [
            'Q283350' => 'deporte',
            'Q115682845' => 'ocupacion',
        ];
        return $mapa[$id] ?? 'recreacion';
    }

    private function storeEntity($externalId, $label, $tipo, $activo = false) 
    { 

        Catalogo::firstOrCreate(
            ['external_id' => $externalId],
            [
                'nombre' => $label,
                'tipo' => $tipo,
                'esta_activo' => $activo, // Solo se aplica si es NUEVO
            ]
        );

    } 
}
