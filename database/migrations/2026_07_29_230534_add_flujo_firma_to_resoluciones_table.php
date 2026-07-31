<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('resoluciones', function (Blueprint $table) {
            $table->foreignId('verificado_por')->nullable()->after('auth_status')->constrained('users')->nullOnDelete();
            $table->timestamp('fecha_verificacion')->nullable()->after('verificado_por');

            $table->foreignId('aprobado_por')->nullable()->after('fecha_verificacion')->constrained('users')->nullOnDelete();
            $table->timestamp('fecha_aprobacion')->nullable()->after('aprobado_por');

            $table->foreignId('rechazado_por')->nullable()->after('fecha_aprobacion')->constrained('users')->nullOnDelete();
            $table->timestamp('fecha_rechazo')->nullable()->after('rechazado_por');

            $table->text('observaciones')->nullable()->after('fecha_rechazo');
        });
    }

    public function down(): void
    {
        Schema::table('resoluciones', function (Blueprint $table) {
            $table->dropForeign(['verificado_por']);
            $table->dropForeign(['aprobado_por']);
            $table->dropForeign(['rechazado_por']);
            $table->dropColumn([
                'verificado_por',
                'fecha_verificacion',
                'aprobado_por',
                'fecha_aprobacion',
                'rechazado_por',
                'fecha_rechazo',
                'observaciones',
            ]);
        });
    }
};
