<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Vecino
 *
 * @property int $id
 * @property string $id_DMQ
 * @property int $user_id
 * @property string $cedula
 * @property string|null $telefono
 * @property Carbon $fecha_registro
 * @property Carbon|null $fecha_cancelacion
 * @property array|null $ocupacion     → JSON: ["Comerciante", "Docente", ...]
 * @property array|null $deportes      → JSON: ["Fútbol", "Natación", ...]
 * @property array|null $recreacion    → JSON: ["Lectura", "Cine", ...]
 * @property bool $is_active
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string $calle_principal
 * @property string $numero
 * @property string $calle_secundaria
 * @property string|null $referencias
 *
 * @property Barrio $barrio
 * @property User $user
 * @property Collection|MetadataVecino[] $metadata_vecinos
 *
 * @package App\Models
 */
class Vecino extends Model
{
	protected $table = 'vecinos';

	protected $casts = [
		'user_id'            => 'integer',
		'fecha_registro'     => 'datetime',
		'fecha_cancelacion'  => 'datetime',
		'is_active'          => 'boolean',
		// ── Campos JSON ──────────────────────────────────────────
		'ocupacion'          => 'array',   // ["Comerciante", "Docente"]
		'deportes'           => 'array',   // ["Fútbol", "Natación"]
		'recreacion'         => 'array',   // ["Lectura", "Cine"]
	];

	protected $hidden = [
		'password',
		'verification_token',
		'two_factor_secret',
		'remember_token',
	];

	protected $fillable = [
		'id_DMQ',
		'user_id',
		'cedula',
		'telefono',
		'fecha_registro',
		'fecha_cancelacion',
		'ocupacion',          // acepta array o JSON string
		'deportes',           // acepta array o JSON string
		'recreacion',         // acepta array o JSON string
		'is_active',
		'calle_principal',
		'numero',
		'calle_secundaria',
		'referencias',
	];

	// ── Relaciones ────────────────────────────────────────────────

	public function barrio()
	{
		return $this->belongsTo(Barrio::class, 'id_DMQ', 'id_DMQ');
	}

	public function user()
	{
		return $this->belongsTo(User::class);
	}

	public function metadata_vecinos()
	{
		return $this->hasMany(MetadataVecino::class);
	}

    // ── Helpers ───────────────────────────────────────────────────

	/**
	 * Agrega un ítem a ocupacion sin duplicar.
	 * Uso: $vecino->agregarOcupacion('Comerciante');
	 */
	public function agregarOcupacion(string $item): void
	{
		$lista = $this->ocupacion ?? [];
		if (! in_array($item, $lista)) {
			$this->ocupacion = [...$lista, $item];
			$this->save();
		}
	}

	/**
	 * Agrega un ítem a deportes sin duplicar.
	 */
	public function agregarDeporte(string $item): void
	{
		$lista = $this->deportes ?? [];
		if (! in_array($item, $lista)) {
			$this->deportes = [...$lista, $item];
			$this->save();
		}
	}

	/**
	 * Agrega un ítem a recreacion sin duplicar.
	 */
	public function agregarRecreacion(string $item): void
	{
		$lista = $this->recreacion ?? [];
		if (! in_array($item, $lista)) {
			$this->recreacion = [...$lista, $item];
			$this->save();
		}
	}
}
