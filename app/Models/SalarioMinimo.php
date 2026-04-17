<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalarioMinimo extends Model
{
    /** @use HasFactory<\Database\Factories\SalarioMinimoFactory> */
    use HasFactory;
    protected $table = 'salariominimo';
    protected $fillable = ['year', 'valor_usd'];

    // 🔗 Relación con porcentajes de infracción
    public function porcentajesInfraccion()
    {
        return $this->hasMany(PorcentajeMultas::class);
    }

    // 📅 Obtener el salario vigente más reciente
    public static function vigente()
    {
        return self::orderByDesc('year')->first();
    }
}
