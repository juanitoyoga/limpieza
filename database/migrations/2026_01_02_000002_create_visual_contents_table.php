<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('visual_contents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('slot_id')->constrained('visual_slots')->cascadeOnDelete();
            $table->foreignId('barrio_id')->nullable()->constrained('barrios')->nullOnDelete();
            // FIX: No crear FK aqui, se agrega despues para romper dependencia circular
            $table->unsignedBigInteger('current_version_id')->nullable();
            $table->enum('estado', ['borrador','pendiente','aprobado','publicado','rechazado','historico'])->default('borrador')->index();
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
            
            $table->index(['slot_id','barrio_id','estado']);
        });
    }
    public function down(): void { Schema::dropIfExists('visual_contents'); }
};
