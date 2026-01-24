<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Assignment extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'nomination_id',
        'assigned_to',
        'role',
        'status',
        'assigned_at',
        'completed_at',
        'notes',
        'blockchain_hash',
        'tx_hash',
        'version',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'assigned_at' => 'datetime',
        'completed_at' => 'datetime',
        'version' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * The attributes that should have default values.
     *
     * @var array<string, mixed>
     */
    protected $attributes = [
        'role' => 'verifier',
        'status' => 'pending',
        'version' => 1,
    ];

    /**
     * Roles disponibles para las asignaciones.
     */
    const ROLE_VERIFIER = 'verifier';
    const ROLE_APPROVER = 'approver';
    const ROLE_AUDITOR = 'auditor';

    /**
     * Estados disponibles para las asignaciones.
     */
    const STATUS_PENDING = 'pending';
    const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_COMPLETED = 'completed';
    const STATUS_REVOKED = 'revoked';

    /**
     * Get the nomination for this assignment.
     */
    public function nomination(): BelongsTo
    {
        return $this->belongsTo(Nomination::class);
    }

    /**
     * Get the user assigned to this task.
     */
    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /**
     * Scope a query to only include pending assignments.
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * Scope a query to only include in progress assignments.
     */
    public function scopeInProgress($query)
    {
        return $query->where('status', self::STATUS_IN_PROGRESS);
    }

    /**
     * Scope a query to only include completed assignments.
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }

    /**
     * Scope a query to only include revoked assignments.
     */
    public function scopeRevoked($query)
    {
        return $query->where('status', self::STATUS_REVOKED);
    }

    /**
     * Scope a query to only include verifier assignments.
     */
    public function scopeVerifiers($query)
    {
        return $query->where('role', self::ROLE_VERIFIER);
    }

    /**
     * Scope a query to only include approver assignments.
     */
    public function scopeApprovers($query)
    {
        return $query->where('role', self::ROLE_APPROVER);
    }

    /**
     * Scope a query to only include auditor assignments.
     */
    public function scopeAuditors($query)
    {
        return $query->where('role', self::ROLE_AUDITOR);
    }

    /**
     * Scope a query to only include assignments for specific user.
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('assigned_to', $userId);
    }

    /**
     * Scope a query to only include assignments for specific nomination.
     */
    public function scopeForNomination($query, $nominationId)
    {
        return $query->where('nomination_id', $nominationId);
    }

    /**
     * Scope a query to only include active assignments (pending or in progress).
     */
    public function scopeActive($query)
    {
        return $query->whereIn('status', [self::STATUS_PENDING, self::STATUS_IN_PROGRESS]);
    }

    /**
     * Scope a query to only include blockchain recorded assignments.
     */
    public function scopeBlockchainRecorded($query)
    {
        return $query->whereNotNull('blockchain_hash');
    }

    /**
     * Scope a query to only include overdue assignments.
     */
    public function scopeOverdue($query, $days = 7)
    {
        return $query->where('assigned_at', '<=', now()->subDays($days))
                    ->whereIn('status', [self::STATUS_PENDING, self::STATUS_IN_PROGRESS]);
    }

    /**
     * Check if the assignment is pending.
     */
    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * Check if the assignment is in progress.
     */
    public function isInProgress(): bool
    {
        return $this->status === self::STATUS_IN_PROGRESS;
    }

    /**
     * Check if the assignment is completed.
     */
    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    /**
     * Check if the assignment is revoked.
     */
    public function isRevoked(): bool
    {
        return $this->status === self::STATUS_REVOKED;
    }

    /**
     * Check if the assignment is active (pending or in progress).
     */
    public function isActive(): bool
    {
        return $this->isPending() || $this->isInProgress();
    }

    /**
     * Check if the assignment is for verifier role.
     */
    public function isVerifier(): bool
    {
        return $this->role === self::ROLE_VERIFIER;
    }

    /**
     * Check if the assignment is for approver role.
     */
    public function isApprover(): bool
    {
        return $this->role === self::ROLE_APPROVER;
    }

    /**
     * Check if the assignment is for auditor role.
     */
    public function isAuditor(): bool
    {
        return $this->role === self::ROLE_AUDITOR;
    }

    /**
     * Check if the assignment is recorded in blockchain.
     */
    public function isRecordedInBlockchain(): bool
    {
        return !is_null($this->blockchain_hash);
    }

    /**
     * Check if the assignment is overdue.
     */
    public function isOverdue($days = 7): bool
    {
        return $this->isActive() && $this->assigned_at->lte(now()->subDays($days));
    }

    /**
     * Mark the assignment as in progress.
     */
    public function markAsInProgress(): void
    {
        $this->update(['status' => self::STATUS_IN_PROGRESS]);
    }

    /**
     * Mark the assignment as completed.
     */
    public function markAsCompleted(string $notes = null): void
    {
        $this->update([
            'status' => self::STATUS_COMPLETED,
            'completed_at' => now(),
            'notes' => $notes ?? $this->notes,
        ]);
    }

    /**
     * Mark the assignment as revoked.
     */
    public function markAsRevoked(string $notes = null): void
    {
        $this->update([
            'status' => self::STATUS_REVOKED,
            'completed_at' => now(),
            'notes' => $notes ?? $this->notes,
        ]);
    }

    /**
     * Reset assignment to pending status.
     */
    public function markAsPending(): void
    {
        $this->update([
            'status' => self::STATUS_PENDING,
            'completed_at' => null,
        ]);
    }

    /**
     * Record blockchain transaction details.
     */
    public function recordBlockchainTransaction(string $blockchainHash, string $txHash): void
    {
        $this->update([
            'blockchain_hash' => $blockchainHash,
            'tx_hash' => $txHash,
        ]);
    }

    /**
     * Calculate the duration of the assignment in days.
     */
    public function getDurationInDays(): ?int
    {
        if (!$this->completed_at) {
            return null;
        }

        return $this->assigned_at->diffInDays($this->completed_at);
    }

    /**
     * Get the role name for display.
     */
    public function getRoleNameAttribute(): string
    {
        return match($this->role) {
            self::ROLE_VERIFIER => 'Verificador',
            self::ROLE_APPROVER => 'Aprobador',
            self::ROLE_AUDITOR => 'Auditor',
            default => 'Desconocido'
        };
    }

    /**
     * Get the status name for display.
     */
    public function getStatusNameAttribute(): string
    {
        return match($this->status) {
            self::STATUS_PENDING => 'Pendiente',
            self::STATUS_IN_PROGRESS => 'En Progreso',
            self::STATUS_COMPLETED => 'Completado',
            self::STATUS_REVOKED => 'Revocado',
            default => 'Desconocido'
        };
    }

    /**
     * Get the blockchain hash with ellipsis for display.
     */
    protected function blockchainHashDisplay(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->blockchain_hash ? 
                substr($this->blockchain_hash, 0, 8) . '...' . substr($this->blockchain_hash, -8) : 
                null,
        );
    }

    /**
     * Get the transaction hash with ellipsis for display.
     */
    protected function txHashDisplay(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->tx_hash ? 
                substr($this->tx_hash, 0, 8) . '...' . substr($this->tx_hash, -8) : 
                null,
        );
    }

    /**
     * Increment the version number.
     */
    public function incrementVersion(): void
    {
        $this->increment('version');
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        // Set assigned_at automatically when creating if not set
        static::creating(function ($assignment) {
            if (empty($assignment->assigned_at)) {
                $assignment->assigned_at = now();
            }
        });
    }
}