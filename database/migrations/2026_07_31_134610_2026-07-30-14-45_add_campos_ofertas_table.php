<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ofertas', function (Blueprint $table) {

            // Documentación
            $table->string('documento_original_path')->nullable()->after('monto_total');
            $table->string('documento_original_hash')->nullable()->after('documento_original_path');
            $table->string('documento_original_mime')->nullable()->after('documento_original_hash');

            // Blockchain
            $table->json('evento_json')->nullable()->after('documento_original_mime');
            $table->string('tx_hash')->nullable()->after('evento_json');
            $table->string('tx_block')->nullable()->after('tx_hash');
            $table->string('blockchain_contract_address')->nullable()->after('tx_block');
            $table->integer('blockchain_block_number')->nullable()->after('blockchain_contract_address');
            $table->timestamp('blockchain_timestamp')->nullable()->after('blockchain_block_number');

            // Estado y responsabilidad
            $table->string('auth_status')->default('Pendiente')->after('estado');

            $table->foreignId('verificado_por')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete()
                ->after('auth_status');

            $table->timestamp('fecha_verificacion')->nullable()->after('verificado_por');

            $table->foreignId('aprobado_por')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete()
                ->after('fecha_verificacion');

            $table->timestamp('fecha_aprobacion')->nullable()->after('aprobado_por');

            $table->foreignId('rechazado_por')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete()
                ->after('fecha_aprobacion');

            $table->timestamp('fecha_rechazo')->nullable()->after('rechazado_por');

            // Auditoría
            $table->text('observaciones')->nullable()->change(); // ya existe, solo asegurar tipo
        });
    }

    public function down(): void
    {
        Schema::table('ofertas', function (Blueprint $table) {

            // Documentación
            $table->dropColumn([
                'documento_original_path',
                'documento_original_hash',
                'documento_original_mime',
            ]);

            // Blockchain
            $table->dropColumn([
                'evento_json',
                'tx_hash',
                'tx_block',
                'blockchain_contract_address',
                'blockchain_block_number',
                'blockchain_timestamp',
            ]);

            // Estado y responsabilidad
            $table->dropForeign(['verificado_por']);
            $table->dropForeign(['aprobado_por']);
            $table->dropForeign(['rechazado_por']);

            $table->dropColumn([
                'auth_status',
                'verificado_por',
                'fecha_verificacion',
                'aprobado_por',
                'fecha_aprobacion',
                'rechazado_por',
                'fecha_rechazo',
            ]);
        });
    }
};
