<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class AuditEvent extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'auditable_id',       // ← reemplaza nomination_id
        'auditable_type',     // ← nuevo
        'user_id',
        'event_type',
        'details',
        'event_at',
        'blockchain_hash',
        'tx_hash',
        'tx_block',
        'version',
        'event_hash',
        'previous_event_hash',

    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'event_at' => 'datetime',
        'details' => 'array', // Si los detalles se almacenan como JSON
        'version' => 'integer',
        'tx_block' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * The attributes that should have default values.
     *
     * @var array<string, mixed>
     */
    protected $attributes = [
        'version' => 1,
    ];

    /**
     * Tipos de eventos disponibles.
     */
    const EVENT_NOMINATION_CREATED = 'nomination_created';
    const EVENT_VERIFICATION_STARTED = 'verification_started';
    const EVENT_VERIFICATION_COMPLETED = 'verification_completed';
    const EVENT_APPROVAL_GRANTED = 'approval_granted';
    const EVENT_APPROVAL_REJECTED = 'approval_rejected';
    const EVENT_ASSIGNMENT_CREATED = 'assignment_created';
    const EVENT_ASSIGNMENT_COMPLETED = 'assignment_completed';
    const EVENT_STATUS_CHANGED = 'status_changed';

    const EVENT_DOCUMENT_UPLOADED = 'PDF cargado';

    const EVENT_BLOCKCHAIN_REGISTERED = 'Proceso_Blockchain_Registrado';


    /**
     * Relación polimórfica — puede ser Nomination, Contrato, Obra, Fondo, etc.
     */
    public function auditable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the user who performed the action.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope a query to only include nomination creation events.
     */
    public function scopeNominationCreated($query)
    {
        return $query->where('event_type', [
            self::EVENT_DOCUMENT_UPLOADED,
            self::EVENT_NOMINATION_CREATED
        ]);
    }

    /**
     * Scope a query to only include verification events.
     */
    public function scopeVerificationEvents($query)
    {
        return $query->whereIn('event_type', [
            self::EVENT_VERIFICATION_STARTED,
            self::EVENT_VERIFICATION_COMPLETED,
        ]);
    }

    /**
     * Scope a query to only include approval events.
     */
    public function scopeApprovalEvents($query)
    {
        return $query->whereIn('event_type', [
            self::EVENT_APPROVAL_GRANTED,
            self::EVENT_APPROVAL_REJECTED,
        ]);
    }

    /**
     * Scope a query to only include assignment events.
     */
    public function scopeAssignmentEvents($query)
    {
        return $query->whereIn('event_type', [
            self::EVENT_ASSIGNMENT_CREATED,
            self::EVENT_ASSIGNMENT_COMPLETED,
        ]);
    }

    // Antes usaban nomination_id — actualizarlos
    public function scopeForNomination($query, $nominationId)
    {
        return $query->where('auditable_type', Nomination::class)
            ->where('auditable_id', $nominationId);
    }

    // Nuevo scope genérico — más útil
    public function scopeForModel($query, Model $model)
    {
        return $query->where('auditable_type', get_class($model))
            ->where('auditable_id', $model->id);
    }
    /**
     * Scope a query to only include events by specific user.
     */
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope a query to only include events within date range.
     */
    public function scopeBetweenDates($query, $startDate, $endDate)
    {
        return $query->whereBetween('event_at', [$startDate, $endDate]);
    }

    /**
     * Scope a query to only include recent events.
     */
    public function scopeRecent($query, $days = 30)
    {
        return $query->where('event_at', '>=', now()->subDays($days));
    }

    /**
     * Scope a query to only include blockchain recorded events.
     */
    public function scopeBlockchainRecorded($query)
    {
        return $query->whereNotNull('blockchain_hash');
    }

    /**
     * Scope a query to only include events of specific type.
     */
    public function scopeOfType($query, $eventType)
    {
        return $query->where('event_type', $eventType);
    }

    /**
     * Check if the event is related to verification.
     */
    public function isVerificationEvent(): bool
    {
        return in_array($this->event_type, [
            self::EVENT_VERIFICATION_STARTED,
            self::EVENT_VERIFICATION_COMPLETED,
        ]);
    }

    /**
     * Check if the event is related to approval.
     */
    public function isApprovalEvent(): bool
    {
        return in_array($this->event_type, [
            self::EVENT_APPROVAL_GRANTED,
            self::EVENT_APPROVAL_REJECTED,
        ]);
    }

    /**
     * Check if the event is related to assignment.
     */
    public function isAssignmentEvent(): bool
    {
        return in_array($this->event_type, [
            self::EVENT_ASSIGNMENT_CREATED,
            self::EVENT_ASSIGNMENT_COMPLETED,
        ]);
    }

    /**
     * Check if the event is a creation event.
     */
    public function isCreationEvent(): bool
    {
        return in_array($this->event_type, [
            self::EVENT_NOMINATION_CREATED,
            self::EVENT_ASSIGNMENT_CREATED,
        ]);
    }

    /**
     * Check if the event is a completion event.
     */
    public function isCompletionEvent(): bool
    {
        return in_array($this->event_type, [
            self::EVENT_VERIFICATION_COMPLETED,
            self::EVENT_ASSIGNMENT_COMPLETED,
        ]);
    }

    /**
     * Check if the event is recorded in blockchain.
     */
    public function isRecordedInBlockchain(): bool
    {
        return !is_null($this->blockchain_hash);
    }

    /**
     * Get the event type name for display.
     */
    public function getEventTypeNameAttribute(): string
    {
        return match ($this->event_type) {
            self::EVENT_NOMINATION_CREATED => 'Nominación Creada',
            self::EVENT_VERIFICATION_STARTED => 'Verificación Iniciada',
            self::EVENT_VERIFICATION_COMPLETED => 'Verificación Completada',
            self::EVENT_APPROVAL_GRANTED => 'Aprobación Concedida',
            self::EVENT_APPROVAL_REJECTED => 'Aprobación Rechazada',
            self::EVENT_ASSIGNMENT_CREATED => 'Asignación Creada',
            self::EVENT_ASSIGNMENT_COMPLETED => 'Asignación Completada',
            self::EVENT_DOCUMENT_UPLOADED => 'PDF cargado',
            self::EVENT_STATUS_CHANGED => 'Estado Cambiado',
            default => 'Evento Desconocido'
        };
    }

    /**
     * Get the event category.
     */
    public function getEventCategoryAttribute(): string
    {
        return match ($this->event_type) {
            self::EVENT_NOMINATION_CREATED => 'creation',
            self::EVENT_VERIFICATION_STARTED, self::EVENT_VERIFICATION_COMPLETED => 'verification',
            self::EVENT_APPROVAL_GRANTED => 'approval',
            self::EVENT_APPROVAL_REJECTED => 'rejected',
            self::EVENT_ASSIGNMENT_CREATED, self::EVENT_ASSIGNMENT_COMPLETED => 'assignment',
            self::EVENT_DOCUMENT_UPLOADED => 'document uploaded',
            self::EVENT_STATUS_CHANGED => 'status',
            default => 'other'
        };
    }

    /**
     * Get the icon for the event type.
     */
    public function getEventIconAttribute(): string
    {
        return match ($this->event_type) {
            self::EVENT_NOMINATION_CREATED => '📝',
            self::EVENT_VERIFICATION_STARTED => '🔍',
            self::EVENT_VERIFICATION_COMPLETED => '✅',
            self::EVENT_APPROVAL_GRANTED => '👍',
            self::EVENT_APPROVAL_REJECTED => '👎',
            self::EVENT_ASSIGNMENT_CREATED => '👤',
            self::EVENT_ASSIGNMENT_COMPLETED => '🏁',
            self::EVENT_DOCUMENT_UPLOADED => '✅',
            self::EVENT_STATUS_CHANGED => '🔄',
            default => '📋'
        };
    }

    /**
     * Record blockchain transaction details.
     */
    public function recordBlockchainTransaction(string $blockchainHash, string $txHash, ?int $txBlock = null): void
    {
        $this->update([
            'blockchain_hash' => $blockchainHash,
            'tx_hash'         => $txHash,
            'tx_block'        => $txBlock,
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
            get: fn() => $this->blockchain_hash ?
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
            get: fn() => $this->tx_hash ?
                substr($this->tx_hash, 0, 8) . '...' . substr($this->tx_hash, -8) :
                null,
        );
    }

    public static function logEvent(
        Model $auditable,      // ← cualquier modelo: Contrato, Nomination, Obra...
        int $userId,
        string $eventType,
        ?array $details = null
    ): self {
        $previous = self::where('auditable_id',   $auditable->id)
            ->where('auditable_type', get_class($auditable))
            ->orderByDesc('event_at')
            ->first();

        $payload = [
            'auditable_id'   => $auditable->id,
            'auditable_type' => get_class($auditable),
            'user_id'        => $userId,
            'event_type'     => $eventType,
            'details'        => $details,
            'previous_hash'  => $previous?->event_hash,
            'timestamp'      => now()->toISOString(),
        ];

        return self::create([
            'auditable_id'        => $auditable->id,
            'auditable_type'      => get_class($auditable),
            'user_id'             => $userId,
            'event_type'          => $eventType,
            'details'             => $details,
            'previous_event_hash' => $previous?->event_hash,
            'event_hash'          => hash('sha256', json_encode($payload)),
            'event_at'            => now(),
        ]);
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        // Set event_at automatically when creating if not set
        static::creating(function ($auditEvent) {
            if (empty($auditEvent->event_at)) {
                $auditEvent->event_at = now();
            }
        });
    }
}
