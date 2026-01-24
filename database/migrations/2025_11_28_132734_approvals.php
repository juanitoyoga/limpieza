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
        Schema::create('approvals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('nomination_id')->constrained('nominations')->onDelete('cascade'); 
            $table->foreignId('approved_by')->constrained('users')->onDelete('cascade'); 
            $table->timestamp('approved_at'); // fecha/hora obligatoria
            $table->enum('decision', ['approved','rejected','revoked'])->default('approved'); 
            $table->text('notes')->nullable(); 
            $table->char('blockchain_hash', 64)->nullable(); // hash del bloque
            $table->char('tx_hash', 64)->nullable(); // hash de la transacción
            $table->unsignedInteger('version')->default(1); // control de versiones
            $table->timestamps();
        
            $table->index(['nomination_id', 'approved_by']); // optimización de consultas
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('approvals');
    }
};
