<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Funcionario
 * 
 * @property int $id
 * @property string $user_role
 * @property int $user_id
 * @property string $email
 * @property int $nomination_id
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
 * @property string $dependencia_dmq
 * @property string $calle_principal
 * @property string $numero
 * @property string $calle_secundaria
 * @property string $referencias
 * 
 * @property User $user
 *
 * @package App\Models
 */
class Funcionario extends Model
{
	protected $table = 'funcionarios';

	protected $casts = [
	
		'user_id' => 'int',
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
		'user_role',
		'user_id',
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
		'dependencia_dmq',
		'calle_principal',
		'numero',
		'calle_secundaria',
		'referencias'
	];

	public function user()
	{
		return $this->belongsTo(User::class);
	}


}
