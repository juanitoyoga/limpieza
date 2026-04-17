<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Approval extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'nomination_id',
        'approved_by',
        'approved_at',
        'decision',
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
        'approved_at' => 'datetime',
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
        'decision' => 'approved',
        'version' => 1,
    ];

    /**
     * Get the nomination being approved.
     */
    public function nomination(): BelongsTo
    {
        return $this->belongsTo(Nomination::class);
    }

    /**
     * Get the user who performed the approval.
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Scope a query to only include approved decisions.
     */
    public function scopeApproved($query)
    {
        return $query->where('decision', 'approved');
    }

    /**
     * Scope a query to only include rejected decisions.
     */
    public function scopeRejected($query)
    {
        return $query->where('decision', 'rejected');
    }

    /**
     * Scope a query to only include revoked decisions.
     */
    public function scopeRevoked($query)
    {
        return $query->where('decision', 'revoked');
    }

    /**
     * Scope a query to only include recent approvals.
     */
    public function scopeRecent($query, $days = 30)
    {
        return $query->where('approved_at', '>=', now()->subDays($days));
    }

    /**
     * Scope a query to only include blockchain recorded approvals.
     */
    public function scopeBlockchainRecorded($query)
    {
        return $query->whereNotNull('blockchain_hash');
    }

    /**
     * Scope a query to only include approvals by specific user.
     */
    public function scopeByApprover($query, $userId)
    {
        return $query->where('approved_by', $userId);
    }

    /**
     * Scope a query to only include approvals for specific nomination.
     */
    public function scopeForNomination($query, $nominationId)
    {
        return $query->where('nomination_id', $nominationId);
    }

    /**
     * Check if the decision is approved.
     */
    public function isApproved(): bool
    {
        return $this->decision === 'approved';
    }

    /**
     * Check if the decision is rejected.
     */
    public function isRejected(): bool
    {
        return $this->decision === 'rejected';
    }

    /**
     * Check if the decision is revoked.
     */
    public function isRevoked(): bool
    {
        return $this->decision === 'revoked';
    }

    /**
     * Check if the approval is recorded in blockchain.
     */
    public function isRecordedInBlockchain(): bool
    {
        return !is_null($this->blockchain_hash);
    }

    /**
     * Check if the approval is positive (approved).
     */
    public function isPositive(): bool
    {
        return $this->isApproved();
    }

    /**
     * Check if the approval is negative (rejected or revoked).
     */
    public function isNegative(): bool
    {
        return $this->isRejected() || $this->isRevoked();
    }

    /**
     * Mark the approval as approved.
     */
    public function markAsApproved(string $notes = null): void
    {
        $this->update([
            'decision' => 'approved',
            'approved_at' => now(),
            'notes' => $notes ?? $this->notes,
        ]);
    }

    /**
     * Mark the approval as rejected.
     */
    public function markAsRejected(string $notes = null): void
    {
        $this->update([
            'decision' => 'rejected',
            'approved_at' => now(),
            'notes' => $notes ?? $this->notes,
        ]);
    }

    /**
     * Mark the approval as revoked.
     */
    public function markAsRevoked(string $notes = null): void
    {
        $this->update([
            'decision' => 'revoked',
            'approved_at' => now(),
            'notes' => $notes ?? $this->notes,
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
     * Increment the version number.
     */
    public function incrementVersion(): void
    {
        $this->increment('version');
    }

    /**
     * Get the approval status for display.
     */
    public function getStatusAttribute(): string
    {
        return match($this->decision) {
            'approved' => 'Aprobado',
            'rejected' => 'Rechazado',
            'revoked' => 'Revocado',
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
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        // Set approved_at automatically when creating if not set
        static::creating(function ($approval) {
            if (empty($approval->approved_at)) {
                $approval->approved_at = now();
            }
        });
    }
}