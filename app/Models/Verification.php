<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Verification extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'nomination_id',
        'verified_by',
        'verified_at',
        'evidence',
        'result',
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
        'verified_at' => 'datetime',
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
        'result' => 'pending',
        'version' => 1,
    ];

    /**
     * Get the nomination being verified.
     */
    public function nomination(): BelongsTo
    {
        return $this->belongsTo(Nomination::class);
    }

    /**
     * Get the user who performed the verification.
     */
    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    /**
     * Scope a query to only include pending verifications.
     */
    public function scopePending($query)
    {
        return $query->where('result', 'pending');
    }

    /**
     * Scope a query to only include passed verifications.
     */
    public function scopePassed($query)
    {
        return $query->where('result', 'passed');
    }

    /**
     * Scope a query to only include failed verifications.
     */
    public function scopeFailed($query)
    {
        return $query->where('result', 'failed');
    }

    /**
     * Scope a query to only include verified records (with verification date).
     */
    public function scopeVerified($query)
    {
        return $query->whereNotNull('verified_at');
    }

    /**
     * Scope a query to only include unverified records.
     */
    public function scopeUnverified($query)
    {
        return $query->whereNull('verified_at');
    }

    /**
     * Scope a query to only include blockchain recorded verifications.
     */
    public function scopeBlockchainRecorded($query)
    {
        return $query->whereNotNull('blockchain_hash');
    }

    /**
     * Check if the verification is pending.
     */
    public function isPending(): bool
    {
        return $this->result === 'pending';
    }

    /**
     * Check if the verification passed.
     */
    public function isPassed(): bool
    {
        return $this->result === 'passed';
    }

    /**
     * Check if the verification failed.
     */
    public function isFailed(): bool
    {
        return $this->result === 'failed';
    }

    /**
     * Check if the verification has been completed (has verification date).
     */
    public function isVerified(): bool
    {
        return !is_null($this->verified_at);
    }

    /**
     * Check if the verification is recorded in blockchain.
     */
    public function isRecordedInBlockchain(): bool
    {
        return !is_null($this->blockchain_hash);
    }

    /**
     * Mark the verification as passed.
     */
    public function markAsPassed(string $evidence = null): void
    {
        $this->update([
            'result' => 'passed',
            'verified_at' => now(),
            'evidence' => $evidence ?? $this->evidence,
        ]);
    }

    /**
     * Mark the verification as failed.
     */
    public function markAsFailed(string $evidence = null): void
    {
        $this->update([
            'result' => 'failed',
            'verified_at' => now(),
            'evidence' => $evidence ?? $this->evidence,
        ]);
    }

    /**
     * Reset verification to pending status.
     */
    public function markAsPending(): void
    {
        $this->update([
            'result' => 'pending',
            'verified_at' => null,
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
}