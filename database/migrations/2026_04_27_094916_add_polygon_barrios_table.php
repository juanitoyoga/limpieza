<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    // database/migrations/xxxx_add_polygon_to_barrios_table.php
    public function up(): void
    {
        Schema::table('barrios', function (Blueprint $table) {
            $table->json('polygon')->nullable()->after('nombre');
        });
    }

    public function down(): void
    {
        Schema::table('barrios', function (Blueprint $table) {
            $table->dropColumn('polygon');
        });
    }
};
