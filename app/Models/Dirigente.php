<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class Dirigente
 *
 * Representa a un dirigente comunitario asignado a un barrio específico.
 * Cada dirigente está vinculado a un usuario y a la nominación que lo originó.
 * Un dirigente pertenece a un solo barrio.
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
 * @property string|null $verification_token
 * @property bool $is_active
 * @property string|null $two_factor_secret
 * @property string|null $two_factor_recovery_codes
 * @property string|null $remember_token
 * @property string $calle_principal
 * @property string $numero
 * @property string $calle_secundaria
 * @property string|null $referencias
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @property Barrio $barrio Barrio al que pertenece el dirigente
 * @property User $user Usuario asociado al dirigente
 * @property Nomination|null $nomination Nominación que originó este registro
 *
 * @package App\Models
 */
class Dirigente extends Model
{
    use HasFactory;

    protected $table = 'dirigentes';

    protected $casts = [
        'barrio_id'         => 'int',
        'user_id'           => 'int',
        'nomination_id'     => 'int',
        'email_verified_at' => 'datetime',
        'last_login_at'     => 'datetime',
        'is_active'         => 'bool',
        'created_at'        => 'datetime',
        'updated_at'        => 'datetime',
    ];

    protected $hidden = [
        'password',
        'verification_token',
        'two_factor_secret',
        'two_factor_recovery_codes',
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
        'calle_principal',
        'numero',
        'calle_secundaria',
        'referencias',
    ];

    protected $attributes = [
        'is_active' => true,
        'timezone'  => 'America/Guayaquil',
        'language'  => 'es',
    ];
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($dirigente) {
            // Ya no se valida "solo un dirigente por barrio".
            // Se valida que el mismo usuario no esté duplicado como dirigente activo del mismo barrio.
            $duplicado = self::where('barrio_id', $dirigente->barrio_id)
                ->where('user_id', $dirigente->user_id)
                ->where('is_active', true)
                ->exists();

            if ($duplicado) {
                throw new \Exception('Este usuario ya es dirigente activo de este barrio.');
            }
        });
    }
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
    public function scopeDistintoDe($query, int $userId)
    {
        return $query->where('user_id', '!=', $userId);
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
