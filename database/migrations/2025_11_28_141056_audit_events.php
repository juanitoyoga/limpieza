<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('audit_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('nomination_id')->constrained('nominations')->onDelete('cascade'); 
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // actor del evento
            $table->enum('event_type', [
                'nomination_created',
                'verification_started',
                'verification_completed',
                'approval_granted',
                'approval_rejected',
                'assignment_created',
                'assignment_completed',
                'status_changed'
            ]);
            $table->text('details')->nullable(); // descripción narrativa o metadatos
            $table->timestamp('event_at'); 
            $table->char('blockchain_hash', 64)->nullable(); // hash del bloque
            $table->char('tx_hash', 64)->nullable(); // hash de la transacción
            $table->unsignedInteger('version')->default(1); // control de versiones
            $table->timestamps();
        
            $table->index(['nomination_id','user_id','event_type']);
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('auditevents');
    }
};
