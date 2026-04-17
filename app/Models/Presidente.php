<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class Presidente
 * 
 * @property int $id
 * @property int $userrole_id
 * @property string $id_DMQ
 * @property int $user_id
 * @property string $email
 * @property Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $phone
 * @property string $timezone
 * @property string $language
 * @property Carbon|null $last_login_at
 * @property string|null $last_login_ip
 * @property string|null $verification_token
 * @property bool $is_		'barrio_id' => 'int',active
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
 *
 * @package App\Models
 */
class Presidente extends Model
{
	protected $table = 'presidentes';

	protected $casts = [
		'userrole_id' => 'int',
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
		'userrole_id',
		'id_DMQ',
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
		'calle_principal',
		'numero',
		'calle_secundaria',
		'referencias'
	];


    public function barrio(): BelongsTo
    {
        return $this->belongsTo(Barrio::class, 'id_DMQ' , 'id_DMQ');
    }


	public function user()
	{
		return $this->belongsTo(User::class);
	}

	public function userrole()
	{
		return $this->belongsTo(Userrole::class);
	}
}
