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
        Schema::create('assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('nomination_id')->constrained('nominations')->onDelete('cascade'); 
            $table->foreignId('assigned_to')->constrained('users')->onDelete('cascade'); // usuario asignado
            $table->enum('role', ['verifier','approver','auditor'])->default('verifier'); // rol de la tarea
            $table->enum('status', ['pending','in_progress','completed','revoked'])->default('pending'); 
            $table->timestamp('assigned_at'); 
            $table->timestamp('completed_at')->nullable(); 
            $table->text('notes')->nullable(); 
            $table->char('blockchain_hash', 64)->nullable(); 
            $table->char('tx_hash', 64)->nullable(); 
            $table->unsignedInteger('version')->default(1); 
            $table->timestamps();
        
            $table->index(['nomination_id','assigned_to','role']);
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assigments');
    }
};
