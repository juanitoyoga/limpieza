<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Http;

class CedulaEcuador implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // 1. Validación básica de longitud y formato
        if (!preg_match('/^[0-9]{10}$/', $value)) {
            $fail('La cédula debe tener 10 dígitos numéricos.');
            return;
        }

        // 2. Consumo del Servicio del Registro Civil (Simulación de API)
        // Sustituye la URL por tu endpoint real o gubernamental
        try {
            $response = Http::timeout(5)->get("https://api.registrocivil.gob.ec/consulta/{$value}");

            if ($response->failed() || !$response->json('existe')) {
                $fail('El número de cédula no se encuentra registrado en el sistema nacional.');
            }
        } catch (\Exception $e) {
            // Si el servicio falla, puedes optar por validar solo el algoritmo de comprobación
            if (!$this->validarAlgoritmo($value)) {
                $fail('La estructura de la cédula es inválida.');
            }
        }
    }

    private function validarAlgoritmo($c): bool {
        // Lógica del dígito verificador (Módulo 10)
        $provincias = 24;
        $d = str_split($c);
        if ($d[0].$d[1] > $provincias || $d[2] > 6) return false;
        $imp = 0; $par = 0;
        for($i=0; $i<9; $i+=2) {
            $t = $d[$i]*2;
            $imp += ($t > 9) ? $t-9 : $t;
            if($i<8) $par += $d[$i+1];
        }
        $suma = $imp + $par;
        $dv = (ceil($suma/10)*10) - $suma;
        return $dv == $d[9];
    }
}
