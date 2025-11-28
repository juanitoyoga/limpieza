<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;


/**
 * Class Userrole
 * 
 * @property int $id
 * @property int $user_id
 * @property string $role
 * @property Carbon $started_at
 * @property Carbon|null $ended_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property User $user
 * @property Collection|Auditor[] $auditores
 * @property Collection|Dirigente[] $dirigentes
 * @property Collection|Funcionario[] $funcionarios
 * @property Collection|Presidente[] $presidentes
 * @property Collection|Supervisor[] $supervisores
 * @property Collection|Vecino[] $vecinos
 *
 * @package App\Models
 */
class Userrole extends Model
{    
	protected $table = 'user_roles';

    protected $fillable = [
        'user_id',
        'role_id',
        'appointment_document', // Documento de nombramiento
        'cessation_document', // Documento de cesación
        'started_at',
        'ended_at',
        'is_active',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
        'is_active' => 'boolean',
    ];


	public function auditores()
	{
		return $this->hasMany(Auditor::class);
	}

	public function dirigentes()
	{
		return $this->hasMany(Dirigente::class);
	}

	public function funcionarios()
	{
		return $this->hasMany(Funcionario::class);
	}

	public function presidentes()
	{
		return $this->hasMany(Presidente::class);
	}

	public function supervisores()
	{
		return $this->hasMany(Supervisor::class);
	}

	public function vecinos()
	{
		return $this->hasMany(Vecino::class, 'userroles_id');
	}

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    public function activate($appointmentDocument, $startedAt)
    {
        $this->update([
            'is_active' => true,
            'appointment_document' => $appointmentDocument,
            'started_at' => $startedAt,
            'ended_at' => null,
            'cessation_document' => null,
        ]);
    }

    public function deactivate($cessationDocument, $endedAt)
    {
        $this->update([
            'is_active' => false,
            'cessation_document' => $cessationDocument,
            'ended_at' => $endedAt,
        ]);
    }

    public function isCurrentlyActive()
    {
        return $this->is_active;
    }

    public function getRoleAssignmentPeriod()
    {
        return $this->started_at->format('Y-m-d') . ' - ' . ($this->ended_at ? $this->ended_at->format('Y-m-d') : 'Actualidad');
    }
}