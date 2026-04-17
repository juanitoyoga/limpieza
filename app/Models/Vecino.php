<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Vecino
 * 
 * @property int $id
 * @property int $userroles_id
 * @property string $id_IMQ
 * @property int $user_id
 * @property string $cedula
 * @property string|null $telefono
 * @property Carbon $fecha_registro
 * @property Carbon $fecha_cancelacion
 * @property string|null $ocupacion
 * @property string|null $deportes
 * @property string|null $recreacion
 * @property string $email
 * @property Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $phone
 * @property string $timezone
 * @property string $language
 * @property Carbon|null $last_login_at
 * @property string|null $last_login_ip
 * @property string|null $verification_token
 * @property bool $is_active
 * @property string|null $two_factor_secret
 * @property string|null $two_factor_recovery_codes
 * @property string|null $remember_token
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string $calle_principal
 * @property string $numero
 * @property string $calle_secundaria
 * @property string $referencias
 * 
 * @property Barrio $barrio
 * @property User $user
 * @property Userrole $userrole
 * @property Collection|MetadataVecino[] $metadata_vecinos
 *
 * @package App\Models
 */
class Vecino extends Model
{
	protected $table = 'vecinos';

	protected $casts = [
		'userroles_id' => 'int',
		
		'user_id' => 'int',
		'fecha_registro' => 'datetime',
		'fecha_cancelacion' => 'datetime',
		'email_verified_at' => 'datetime',
		'last_login_at' => 'datetime',
		'is_active' => 'bool'
	];

	protected $hidden = [
		'password',
		'verification_token',
		'two_factor_secret',
		'remember_token'
	];

	protected $fillable = [
		'userroles_id',
		'id_DMQ',
		'user_id',
		'cedula',
		'telefono',
		'fecha_registro',
		'fecha_cancelacion',
		'ocupacion',
		'deportes',
		'recreacion',
		'email',
		'email_verified_at',
		'password',
		'phone',
		'timezone',
		'language',
		'last_login_at',
		'last_login_ip',
		'verification_token',
		'is_active',
		'two_factor_secret',
		'two_factor_recovery_codes',
		'remember_token',
		'calle_principal',
		'numero',
		'calle_secundaria',
		'referencias'
	];

	public function barrio()
	{
		return $this->belongsTo(Barrio::class, 'id_DMQ' , 'id_DMQ');
	}

	public function user()
	{
		return $this->belongsTo(User::class);
	}

	public function userrole()
	{
		return $this->belongsTo(Userrole::class, 'userroles_id');
	}

	public function metadata_vecinos()
	{
		return $this->hasMany(MetadataVecino::class);
	}
}
