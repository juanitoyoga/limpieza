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
        Schema::create('auditores', function (Blueprint $table) {
            $table->id(); // Clave primaria: id único del funcionario que actua como auditor
            $table->foreignId('user_roles_id')->constrained()->onDelete('cascade'); // Clave foránea: referencia al rol activo
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete(); // Clave foranea: referencia al usuario
  

            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('phone')->nullable();

            $table->string('timezone')->default('UTC');
            $table->string('language')->default('en');
            $table->timestamp('last_login_at')->nullable();
            $table->string('last_login_ip')->nullable();
            $table->string('verification_token')->nullable();
            $table->boolean('is_active')->default(true);

            $table->text('two_factor_secret')->nullable();
            $table->text('two_factor_recovery_codes')->nullable();
  
            $table->rememberToken();
  
            $table->timestamps();
    /**
     * Datos del auditor para cerrar movimientos de egresos y billetera barrial y notificaciones como tal
     */            
            $table->string(column: 'dependencia_dmq');
            $table->string(column: 'calle_principal');
            $table->string(column: 'numero');
            $table->string(column: 'calle_secundaria');
            $table->string(column: 'referencias');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('auditores');
    }
};
