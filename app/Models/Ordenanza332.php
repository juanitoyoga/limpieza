<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ordenanza332 extends Model
{
    /** @use HasFactory<\Database\Factories\Ordenanza332Factory> */
    use HasFactory;
    protected $table = 'ordenanza332';
    protected $fillable = ['codigo', 'descripcion', 'tipo', 'nivel_gravedad'];

    // 🔗 Relación con porcentajes históricos
    public function porcentajes()
    {
        return $this->hasMany(PorcentajeMultas::class);
    }

    // 📊 Obtener el porcentaje vigente
    public function porcentajeVigente()
    {
        return $this->porcentajes()
            ->where('salariominimo_id', SalarioMinimo::vigente()->id)
            ->latest()
            ->first();
    }

    // 💰 Calcular multa actual
    public function multaActual()
    {
        $porcentaje = $this->porcentajeVigente();
        if (!$porcentaje) return null;

        $salario = $porcentaje->salarioMinimo->valor_usd;
        return ($porcentaje->porcentaje / 100) * $salario;
    }

    public function denuncias()
    {
        return $this->hasMany(Denuncia::class);
    }
}
