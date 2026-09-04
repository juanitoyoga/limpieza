<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vecinos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('userroles_id')->constrained('user_roles')->onDelete('cascade');
            $table->string('id_DMQ');
            $table->foreign('id_DMQ')->references('id_DMQ')->on('barrios');
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('cedula')->unique();
            $table->string('telefono')->nullable();
            $table->date('fecha_registro')->default(now()->toDateString());
            $table->date('fecha_cancelacion')->nullable();
            $table->string('ocupacion')->nullable();
            $table->string('deportes')->nullable();
            $table->string('recreacion')->nullable();
            $table->string('calle_principal');
            $table->string('numero');
            $table->string('calle_secundaria');
            $table->string('referencias')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vecinos');
    }
};
