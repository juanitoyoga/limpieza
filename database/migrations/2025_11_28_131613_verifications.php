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
        Schema::create('verifications', function (Blueprint $table) {

                $table->id();
                $table->foreignId('nomination_id')->constrained('nominations')->onDelete('cascade'); 
                $table->foreignId('verified_by')->constrained('users')->onDelete('cascade'); 
                $table->timestamp('verified_at')->nullable(); // fecha/hora real de verificación
                $table->text('evidence')->nullable(); // evidencia adjunta
                $table->enum('result', ['pending','passed','failed'])->default('pending'); 
                $table->char('blockchain_hash', 64)->nullable(); // hash del bloque
                $table->char('tx_hash', 64)->nullable(); // hash de la transacción
                $table->unsignedInteger('version')->default(1); // control de versiones
                $table->timestamps();
                
            });
            
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('verifications');
    }
};
