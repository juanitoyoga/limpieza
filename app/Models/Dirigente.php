<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class Dirigente
 * 
 * Representa a un dirigente comunitario asignado a un barrio específico.
 * Cada dirigente está vinculado a un usuario y tiene una dirección específica.
 * Un dirigente pertenece a un solo barrio (relación inversa de Barrio->dirigente).
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
 * @property Carbon|null $deleted_at
 * 
 * @property Barrio $barrio Barrio al que pertenece el dirigente
 * @property User $user Usuario asociado al dirigente
 * @property Userrole $userrole Rol del usuario
 *
 * @package App\Models
 */
class Dirigente extends Model
{
    use SoftDeletes, HasFactory;

    /**
     * Nombre de la tabla en la base de datos
     *
     * @var string
     */
    protected $table = 'dirigentes';

    /**
     * Atributos que deben ser casteados a tipos nativos
     *
     * @var array
     */
    protected $casts = [
        'userrole_id' => 'int',
        'user_id' => 'int',
        'email_verified_at' => 'datetime',
        'last_login_at' => 'datetime',
        'is_active' => 'bool',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Atributos que deben ocultarse en arrays
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'verification_token',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'remember_token',
    ];

    /**
     * Atributos asignables en masa
     *
     * @var array
     */
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
        'calle_principal',
        'numero',
        'calle_secundaria',
        'referencias',
    ];

    /**
     * Valores predeterminados para los atributos
     *
     * @var array
     */
    protected $attributes = [
        'is_active' => true,
        'timezone' => 'America/Guayaquil',
        'language' => 'es',
    ];

    /**
     * Obtiene el barrio al que pertenece el dirigente (relación muchos a uno)
     * Relación inversa de Barrio->dirigente()
     *
     * @return BelongsTo
     */
    public function barrio(): BelongsTo
    {
        return $this->belongsTo(Barrio::class, 'id_DMQ' , 'id_DMQ');
    }

    /**
     * Obtiene el usuario asociado al dirigente
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Obtiene el rol del usuario
     *
     * @return BelongsTo
     */
    public function userrole(): BelongsTo
    {
        return $this->belongsTo(Userrole::class);
    }

    /**
     * Scope para obtener solo dirigentes activos
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActivos($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope para obtener dirigentes por barrio
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $barrioId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePorBarrio($query, int $barrioId)
    {
        return $query->where('barrio_id', $barrioId);
    }

    /**
     * Scope para obtener dirigentes con email verificado
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeVerificados($query)
    {
        return $query->whereNotNull('email_verified_at');
    }

    /**
     * Scope para obtener dirigentes con autenticación de dos factores habilitada
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeConDosFactor($query)
    {
        return $query->whereNotNull('two_factor_secret');
    }

    /**
     * Verifica si el email del dirigente está verificado
     *
     * @return bool
     */
    public function tieneEmailVerificado(): bool
    {
        return !is_null($this->email_verified_at);
    }

    /**
     * Verifica si el dirigente tiene autenticación de dos factores habilitada
     *
     * @return bool
     */
    public function tieneDosFactor(): bool
    {
        return !is_null($this->two_factor_secret);
    }

    /**
     * Verifica si el dirigente está activo
     *
     * @return bool
     */
    public function estaActivo(): bool
    {
        return $this->is_active === true;
    }

    /**
     * Obtiene la dirección completa del dirigente
     *
     * @return string
     */
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

    /**
     * Obtiene el nombre completo del dirigente desde el usuario
     *
     * @return string|null
     */
    public function getNombreCompletoAttribute(): ?string
    {
        return $this->user ? $this->user->nombre_completo : null;
    }

    /**
     * Obtiene el nombre del barrio asignado
     *
     * @return string|null
     */
    public function getNombreBarrioAttribute(): ?string
    {
        return $this->barrio ? $this->barrio->nombre : null;
    }

    /**
     * Verifica si el dirigente ha iniciado sesión en los últimos N días
     *
     * @param int $dias Número de días (por defecto 30)
     * @return bool
     */
    public function haIniciadoSesionReciente(int $dias = 30): bool
    {
        if (!$this->last_login_at) {
            return false;
        }
        
        return $this->last_login_at->diffInDays(now()) <= $dias;
    }

    /**
     * Registra el último inicio de sesión
     *
     * @param string|null $ip
     * @return bool
     */
    public function registrarLogin(?string $ip = null): bool
    {
        return $this->update([
            'last_login_at' => now(),
            'last_login_ip' => $ip ?? request()->ip(),
        ]);
    }

    /**
     * Formatea el teléfono para mostrar
     *
     * @return string|null
     */
    public function getTelefonoFormateadoAttribute(): ?string
    {
        if (!$this->phone) {
            return null;
        }
        
        // Formato: +593 99 999 9999
        $phone = preg_replace('/\D/', '', $this->phone);
        
        if (strlen($phone) === 10) {
            return sprintf('+593 %s %s %s', 
                substr($phone, 0, 2),
                substr($phone, 2, 3),
                substr($phone, 5)
            );
        }
        
        return $this->phone;
    }

    /**
     * Boot del modelo para eventos
     */
    protected static function boot()
    {
        parent::boot();

        // Evento antes de crear un dirigente
        static::creating(function ($dirigente) {
            // Validar que no exista otro dirigente activo en el mismo barrio
            $existeDirigente = self::where('barrio_id', $dirigente->barrio_id)
                ->where('is_active', true)
                ->exists();
            
            if ($existeDirigente) {
                throw new \Exception('Ya existe un dirigente activo para este barrio.');
            }
        });
    }
}