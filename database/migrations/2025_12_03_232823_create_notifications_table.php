<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->uuid('id')->primary(); // Laravel usa UUID por defecto en notificaciones
            $table->string('type'); // Tipo de notificación (ej: App\Notifications\UserRegistered)
            $table->morphs('notifiable'); // Relación polimórfica con usuarios u otros modelos
            $table->text('data'); // Contenido de la notificación en JSON
            $table->timestamp('read_at')->nullable(); // Fecha de lectura
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};

