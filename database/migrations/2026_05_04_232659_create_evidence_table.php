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
        Schema::create('evidences', function (Blueprint $table) {

            $table->id();
            $table->string('file_path', 255);
            $table->double('latitude');
            $table->double('longitude');
            $table->bigInteger('timestamp_utc');
            $table->string('device_id', 100);
            $table->string('evidence_hash', 128);
            $table->string('signature', 128);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('evidences');
    }
};
