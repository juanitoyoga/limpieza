<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MediaUpload extends Model
{
    protected $table = 'media_uploads';

    protected $fillable = [
        'uuid',
        'user_id',
        'ruta_archivo',
        'mime_type',
        'tamano_bytes',
        'hash_sha256',
        'capturado_en_campo_at',
    ];

    protected $casts = [
        'tamano_bytes'           => 'integer',
        'capturado_en_campo_at'  => 'datetime',
    ];

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
