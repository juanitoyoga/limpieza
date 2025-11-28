<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class MetadataVecino
 * 
 * @property int $id
 * @property int $vecino_id
 * @property string $tipo
 * @property array $datos
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Vecino $vecino
 *
 * @package App\Models
 */
class MetadataVecino extends Model
{
	protected $table = 'metadata_vecinos';

	protected $casts = [
		'vecino_id' => 'int',
		'datos' => 'json'
	];

	protected $fillable = [
		'vecino_id',
		'tipo',
		'datos'
	];

	public function vecino()
	{
		return $this->belongsTo(Vecino::class);
	}
}
