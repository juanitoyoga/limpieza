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
        Schema::create('denuncias', function (Blueprint $table) {

            $table->id();
            $table->foreignId('vecino_id')->constrained('vecinos')->onDelete('cascade');
            $table->foreignId('ordenanza332_id')->constrained('ordenanza332')->onDelete('cascade');
            $table->foreignId('dirigente_id')->nullable()->constrained('dirigentes')->onDelete('set null');
            $table->foreignId('funcionario_id')->nullable()->constrained('funcionarios')->onDelete('set null');

            $table->string('direccion', 255);
            $table->text('descripcion');
            $table->dateTime('fecha_denuncia')->useCurrent();
            $table->enum('estado', ['pendiente', 'verificada', 'aprobada', 'rechazada', 'sancionada'])->default('pendiente');
            $table->decimal('multa_calculada', 8, 2)->nullable();

            // Ubicación geográfica
            $table->decimal('latitud', 10, 7)->nullable();
            $table->decimal('longitud', 10, 7)->nullable();

            // Datos de dispositivo móvil
            $table->string('app_uuid', 64)->nullable()->unique();
            $table->string('device_id', 128)->nullable();
            $table->string('os_version', 50)->nullable();
            $table->string('app_version', 20)->nullable();
            $table->boolean('synced')->default(false);
            $table->timestamp('synced_at')->nullable();

            // Blockchain
            $table->string('file_hash', 128)->nullable(); // Hash del archivo o metadatos
            $table->string('tx_hash', 128)->nullable(); // Transacción blockchain
            $table->string('blockchain_status', 20)->default('pending'); // pending|confirmed|failed
            $table->boolean('verified_on_chain')->default(false);

            $table->timestamps();
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('denuncias');
    }
};
