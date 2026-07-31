<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class Presidente
 *
 * @property int $id
 * @property int $barrio_id
 * @property int $user_id
 * @property int|null $nomination_id
 * @property string $email
 * @property string $role_name
 * @property Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $phone
 * @property string $timezone
 * @property string $language
 * @property Carbon|null $last_login_at
 * @property string|null $last_login_ip
 * @property bool $is_active
 * @property string $calle_principal
 * @property string $numero
 * @property string $calle_secundaria
 * @property string $referencias
 *
 * @property Barrio $barrio
 * @property User $user
 * @property Nomination|null $nomination
 *
 * @package App\Models
 */
class Presidente extends Model
{
	protected $table = 'presidentes';

	protected $casts = [
		'barrio_id'         => 'int',
		'user_id'           => 'int',
		'nomination_id'     => 'int',
		'email_verified_at' => 'datetime',
		'last_login_at'     => 'datetime',
		'is_active'         => 'bool',
	];

	protected $hidden = [
		'password',
		'verification_token',
		'two_factor_secret',
		'remember_token',
	];

	protected $fillable = [
		'barrio_id',
		'user_id',
		'nomination_id',
		'email',
		'role_name',
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
		'referencias',
	];

	public function barrio(): BelongsTo
	{
		return $this->belongsTo(Barrio::class, 'barrio_id');
	}

	public function user(): BelongsTo
	{
		return $this->belongsTo(User::class);
	}

	public function nomination(): BelongsTo
	{
		return $this->belongsTo(Nomination::class);
	}

	protected static function boot()
	{
		parent::boot();

		static::creating(function ($presidente) {
			$existePresidente = self::where('barrio_id', $presidente->barrio_id)
				->where('is_active', true)
				->exists();

			if ($existePresidente) {
				throw new \Exception('Ya existe un presidente activo para este barrio.');
			}
		});
	}


	public function scopeActivos($query)
	{
		return $query->where('is_active', true);
	}

	public function scopePorBarrio($query, int $barrioId)
	{
		return $query->where('barrio_id', $barrioId);
	}

	public function scopeVerificados($query)
	{
		return $query->whereNotNull('email_verified_at');
	}

	public function scopeConDosFactor($query)
	{
		return $query->whereNotNull('two_factor_secret');
	}

	public function tieneEmailVerificado(): bool
	{
		return !is_null($this->email_verified_at);
	}

	public function tieneDosFactor(): bool
	{
		return !is_null($this->two_factor_secret);
	}

	public function estaActivo(): bool
	{
		return $this->is_active === true;
	}

	public function getDireccionCompletaAttribute(): string
	{
		$direccion = "{$this->calle_principal} #{$this->numero}";

		if ($this->calle_secundaria) {
			$direccion .= " y {$this->calle_secundaria}";
		}

		if ($this->referencias) {
			$direccion .= " - {$this->referencias}";
		}

		return $direccion;
	}

	public function getNombreCompletoAttribute(): ?string
	{
		return $this->user ? $this->user->nombre_completo : null;
	}

	public function getNombreBarrioAttribute(): ?string
	{
		return $this->barrio ? $this->barrio->nombre : null;
	}

	public function haIniciadoSesionReciente(int $dias = 30): bool
	{
		if (!$this->last_login_at) {
			return false;
		}

		return $this->last_login_at->diffInDays(now()) <= $dias;
	}

	public function registrarLogin(?string $ip = null): bool
	{
		return $this->update([
			'last_login_at' => now(),
			'last_login_ip' => $ip ?? request()->ip(),
		]);
	}

	public function getTelefonoFormateadoAttribute(): ?string
	{
		if (!$this->phone) {
			return null;
		}

		$phone = preg_replace('/\D/', '', $this->phone);

		if (strlen($phone) === 10) {
			return sprintf(
				'+593 %s %s %s',
				substr($phone, 0, 2),
				substr($phone, 2, 3),
				substr($phone, 5)
			);
		}

		return $this->phone;
	}
}
