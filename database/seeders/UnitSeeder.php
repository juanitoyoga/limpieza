<?php

namespace Database\Seeders;

use App\Models\Unit;
use Illuminate\Database\Seeder;

class UnitSeeder extends Seeder
{
    public function run(): void
    {
        $units = [
            // ==================== LONGITUD ====================
            ['name' => 'Kilómetro', 'symbol' => 'km', 'category' => 'Longitud', 'system' => 'Métrico SI'],
            ['name' => 'Hectómetro', 'symbol' => 'hm', 'category' => 'Longitud', 'system' => 'Métrico SI'],
            ['name' => 'Decámetro', 'symbol' => 'dam', 'category' => 'Longitud', 'system' => 'Métrico SI'],
            ['name' => 'Metro', 'symbol' => 'm', 'category' => 'Longitud', 'system' => 'Métrico SI'],
            ['name' => 'Decímetro', 'symbol' => 'dm', 'category' => 'Longitud', 'system' => 'Métrico SI'],
            ['name' => 'Centímetro', 'symbol' => 'cm', 'category' => 'Longitud', 'system' => 'Métrico SI'],
            ['name' => 'Milímetro', 'symbol' => 'mm', 'category' => 'Longitud', 'system' => 'Métrico SI'],
            ['name' => 'Micrómetro', 'symbol' => 'µm', 'category' => 'Longitud', 'system' => 'Métrico SI'],
            ['name' => 'Nanómetro', 'symbol' => 'nm', 'category' => 'Longitud', 'system' => 'Métrico SI'],
            ['name' => 'Pulgada', 'symbol' => 'in', 'category' => 'Longitud', 'system' => 'Imperial'],
            ['name' => 'Pie', 'symbol' => 'ft', 'category' => 'Longitud', 'system' => 'Imperial'],
            ['name' => 'Yarda', 'symbol' => 'yd', 'category' => 'Longitud', 'system' => 'Imperial'],
            ['name' => 'Milla', 'symbol' => 'mi', 'category' => 'Longitud', 'system' => 'Imperial'],
            ['name' => 'Milla náutica', 'symbol' => 'nmi', 'category' => 'Longitud', 'system' => 'Navegación'],

            // ==================== PESO / MASA ====================
            ['name' => 'Tonelada métrica', 'symbol' => 't', 'category' => 'Peso', 'system' => 'Métrico SI'],
            ['name' => 'Kilogramo', 'symbol' => 'kg', 'category' => 'Peso', 'system' => 'Métrico SI'],
            ['name' => 'Hectogramo', 'symbol' => 'hg', 'category' => 'Peso', 'system' => 'Métrico SI'],
            ['name' => 'Decagramo', 'symbol' => 'dag', 'category' => 'Peso', 'system' => 'Métrico SI'],
            ['name' => 'Gramo', 'symbol' => 'g', 'category' => 'Peso', 'system' => 'Métrico SI'],
            ['name' => 'Decigramo', 'symbol' => 'dg', 'category' => 'Peso', 'system' => 'Métrico SI'],
            ['name' => 'Centigramo', 'symbol' => 'cg', 'category' => 'Peso', 'system' => 'Métrico SI'],
            ['name' => 'Miligramo', 'symbol' => 'mg', 'category' => 'Peso', 'system' => 'Métrico SI'],
            ['name' => 'Microgramo', 'symbol' => 'µg', 'category' => 'Peso', 'system' => 'Métrico SI'],
            ['name' => 'Libra', 'symbol' => 'lb', 'category' => 'Peso', 'system' => 'Imperial'],
            ['name' => 'Onza', 'symbol' => 'oz', 'category' => 'Peso', 'system' => 'Imperial'],
            ['name' => 'Arroba', 'symbol' => '@', 'category' => 'Peso', 'system' => 'Tradicional'],

            // ==================== VOLUMEN ====================
            ['name' => 'Metro cúbico', 'symbol' => 'm³', 'category' => 'Volumen', 'system' => 'Métrico SI'],
            ['name' => 'Centímetro cúbico', 'symbol' => 'cm³', 'category' => 'Volumen', 'system' => 'Métrico SI'],
            ['name' => 'Kilolitro', 'symbol' => 'kL', 'category' => 'Volumen', 'system' => 'Métrico SI'],
            ['name' => 'Hectolitro', 'symbol' => 'hL', 'category' => 'Volumen', 'system' => 'Métrico SI'],
            ['name' => 'Decalitro', 'symbol' => 'daL', 'category' => 'Volumen', 'system' => 'Métrico SI'],
            ['name' => 'Litro', 'symbol' => 'L', 'category' => 'Volumen', 'system' => 'Métrico SI'],
            ['name' => 'Decilitro', 'symbol' => 'dL', 'category' => 'Volumen', 'system' => 'Métrico SI'],
            ['name' => 'Centilitro', 'symbol' => 'cL', 'category' => 'Volumen', 'system' => 'Métrico SI'],
            ['name' => 'Mililitro', 'symbol' => 'mL', 'category' => 'Volumen', 'system' => 'Métrico SI'],
            ['name' => 'Galón (EE. UU.)', 'symbol' => 'gal', 'category' => 'Volumen', 'system' => 'Imperial'],
            ['name' => 'Cuarta (Quart)', 'symbol' => 'qt', 'category' => 'Volumen', 'system' => 'Imperial'],
            ['name' => 'Pinta', 'symbol' => 'pt', 'category' => 'Volumen', 'system' => 'Imperial'],
            ['name' => 'Onza líquida', 'symbol' => 'fl oz', 'category' => 'Volumen', 'system' => 'Imperial'],

            // ==================== SUPERFICIE ====================
            ['name' => 'Kilómetro cuadrado', 'symbol' => 'km²', 'category' => 'Superficie', 'system' => 'Métrico SI'],
            ['name' => 'Hectárea', 'symbol' => 'ha', 'category' => 'Superficie', 'system' => 'Métrico SI'],
            ['name' => 'Área', 'symbol' => 'a', 'category' => 'Superficie', 'system' => 'Métrico SI'],
            ['name' => 'Metro cuadrado', 'symbol' => 'm²', 'category' => 'Superficie', 'system' => 'Métrico SI'],
            ['name' => 'Centímetro cuadrado', 'symbol' => 'cm²', 'category' => 'Superficie', 'system' => 'Métrico SI'],
            ['name' => 'Acre', 'symbol' => 'ac', 'category' => 'Superficie', 'system' => 'Imperial'],

            // ==================== TIEMPO ====================
            ['name' => 'Milisegundo', 'symbol' => 'ms', 'category' => 'Tiempo', 'system' => 'Métrico SI'],
            ['name' => 'Segundo', 'symbol' => 's', 'category' => 'Tiempo', 'system' => 'Métrico SI'],
            ['name' => 'Minuto', 'symbol' => 'min', 'category' => 'Tiempo', 'system' => 'Estándar'],
            ['name' => 'Hora', 'symbol' => 'h', 'category' => 'Tiempo', 'system' => 'Estándar'],
            ['name' => 'Día', 'symbol' => 'd', 'category' => 'Tiempo', 'system' => 'Estándar'],

            // ==================== TEMPERATURA ====================
            ['name' => 'Grado Celsius', 'symbol' => '°C', 'category' => 'Temperatura', 'system' => 'Métrico SI'],
            ['name' => 'Kelvin', 'symbol' => 'K', 'category' => 'Temperatura', 'system' => 'Métrico SI'],
            ['name' => 'Grado Fahrenheit', 'symbol' => '°F', 'category' => 'Temperatura', 'system' => 'Imperial'],
        ];

        foreach ($units as $unit) {
            Unit::firstOrCreate(
                ['symbol' => $unit['symbol']],
                [
                    'name' => $unit['name'],
                    'category' => $unit['category'],
                    'system' => $unit['system'],
                ]
            );
        }
    }
}
