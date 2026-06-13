<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('denuncias', function (Blueprint $table) {
            $table->string('evidencia_path')->nullable()->after('longitud');
            $table->enum('evidencia_tipo', ['foto', 'video'])->nullable()->after('evidencia_path');
        });
    }

    public function down(): void
    {
        Schema::table('denuncias', function (Blueprint $table) {
            $table->dropColumn(['evidencia_path', 'evidencia_tipo']);
        });
    }
};
