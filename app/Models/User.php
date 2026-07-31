<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Cache;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    protected $table = 'users';
    protected $primaryKey = 'id';

    protected $fillable = [
        'tipo_id',
        'nro_id',
        'first_name',
        'last_name',
        'role_name',
        'transition_role',
        'email',
        'password',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'phone',
        'birthdate',
        'gender',
        'avatar',
        'timezone',
        'language',
        'last_login_at',
        'last_login_ip',
        'verification_token',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_secret',
        'two_factor_recovery_codes',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'two_factor_confirmed_at' => 'datetime',
            'birthdate' => 'date',
            'last_login_at' => 'datetime',
            'deleted_at' => 'datetime',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'is_active' => 'boolean',
            'password' => 'hashed',
        ];
    }

    // 🔎 Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
    public function scopeByEmail($query, string $email)
    {
        return $query->where('email', $email);
    }
    public function scopeByNroId($query, string $nroId)
    {
        return $query->where('nro_id', $nroId);
    }
    public function scopeByName($query, string $lastName, string $firstName = null)
    {
        $query->where('last_name', $lastName);
        if ($firstName) $query->where('first_name', $firstName);
        return $query;
    }
    public function scopeCreatedRecently($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    // Accessors
    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function initials(): string
    {
        return Str::of($this->first_name . ' ' . $this->last_name)
            ->explode(' ')
            ->map(fn(string $name) => Str::substr($name, 0, 1))
            ->implode('');
    }

    protected function password(): Attribute
    {
        return Attribute::make(
            set: fn($value) => Hash::needsRehash($value)
                ? Hash::make($value)
                : $value
        );
    }

    public function generateVerificationToken(): void
    {
        $this->verification_token = Str::random(60);
        $this->save();
    }

    public function getAvatarUrlAttribute(): string
    {
        return $this->avatar
            ? asset("storage/avatars/{$this->avatar}")
            : asset("images/default-avatar.jpg");
    }


    /**
     * Obtiene el ID del usuario Admin del sistema, usado para atribuir
     * acciones automatizadas (jobs, comandos) en el log de auditoría.
     *
     * @return int
     */
    public static function getSistemaAdminId(): int
    {
        return Cache::remember('sistema.admin_user_id', now()->addHour(), function () {
            $admin = static::where('role_name', 'Admin')->first();

            if (!$admin) {
                throw new \RuntimeException('No existe ningún usuario con role_name Admin para registrar auditoría del sistema.');
            }

            return $admin->id;
        });
    }
    // Relaciones

    /**
     * Un usuario pertenece a un rol
     */
    // app/Models/User.php
    public function getRole(): BelongsTo
    {
        return $this->belongsTo(
            Role::class,
            'role_name', // users.role_name
            'name'       // roles.name
        );
    }


    public function auditor()
    {
        return $this->hasOne(Auditor::class);
    }
    public function dirigente()
    {
        return $this->hasOne(Dirigente::class);
    }
    public function funcionario()
    {
        return $this->hasOne(Funcionario::class);
    }
    public function presidente()
    {
        return $this->hasOne(Presidente::class);
    }
    public function supervisor()
    {
        return $this->hasOne(Supervisor::class);
    }

    public function vecino()
    {
        return $this->hasOne(Vecino::class);
    }
}
