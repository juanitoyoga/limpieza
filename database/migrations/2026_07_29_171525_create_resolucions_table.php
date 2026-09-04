<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('resoluciones', function (Blueprint $table) {
            $table->id();

            $table->string('codigo')->unique();
            $table->foreignId('barrio_id')->constrained('barrios')->cascadeOnDelete();

            $table->string('titulo');
            $table->text('descripcion')->nullable();
            $table->string('tipo', 100)->nullable();
            $table->date('fecha_resolucion')->nullable();

            $table->string('documento_original_path')->nullable();
            $table->string('documento_original_hash', 128)->nullable();
            $table->string('documento_original_mime', 100)->nullable();
            $table->unsignedInteger('numero_firmas')->default(0);

            $table->json('evento_json')->nullable();

            $table->string('blockchain_tx_hash')->nullable();
            $table->string('blockchain_network', 50)->nullable();
            $table->string('blockchain_contract_address', 191)->nullable();
            $table->unsignedBigInteger('blockchain_block_number')->nullable();
            $table->timestamp('blockchain_timestamp')->nullable();

            $table->string('auth_status', 30)->default('pendiente');

            $table->timestamps();

            $table->index(['barrio_id', 'fecha_resolucion']);
            $table->index('auth_status');
            $table->index('blockchain_tx_hash');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('resoluciones');
    }
};
