<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PorcentajeMultas extends Model
{
    use HasFactory;

    protected $table = 'porcentajemultas';

    protected $fillable = [
        'ordenanza332_id',
        'salariominimo_id',
        'porcentaje'
    ];

    // Relación correcta con Ordenanza332
    public function ordenanza332()
    {
        return $this->belongsTo(Ordenanza332::class, 'ordenanza332_id', 'id');
    }

    // Relación correcta con SalarioMinimo
    public function salarioMinimo()
    {
        return $this->belongsTo(SalarioMinimo::class, 'salariominimo_id', 'id');
    }

    // Cálculo de multa
    public function calcularMulta()
    {
        return ($this->porcentaje / 100) * $this->salarioMinimo->valor_usd;
    }
}
