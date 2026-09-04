<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Almacena archivos binarios (foto/video) subidos desde la app móvil,
     * desacoplado del sync de metadatos (hitos/evidencias). Se sube primero
     * el archivo -> se obtiene una referencia por uuid -> luego el sync JSON
     * de evidencias referencia ese uuid en vez de reenviar el binario.
     *
     * El uuid es generado en el cliente (Android) al momento de capturar,
     * por lo que reintentos de subida (ej. tras perder señal) son idempotentes:
     * si el uuid ya existe, se retorna el registro existente sin duplicar.
     */
    public function up(): void
    {
        Schema::create('media_uploads', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('user_id')->constrained();

            $table->string('ruta_archivo');
            $table->string('mime_type');
            $table->unsignedBigInteger('tamano_bytes');
            $table->string('hash_sha256')->nullable(); // integridad, útil si se audita en blockchain

            $table->timestamp('capturado_en_campo_at')->nullable(); // timestamp del dispositivo
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('media_uploads');
    }
};
