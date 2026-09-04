<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('visual_slots', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique(); // carrusel_principal, noticias, logos_auspiciantes, banners_comerciales, galeria_fotos
            $table->string('label');
            $table->string('description')->nullable();
            $table->enum('scope', ['global', 'por_barrio'])->default('global');
            $table->json('allowed_fields'); // ['titulo','resumen','cuerpo','imagen','orden','link_url','fecha_inicio','fecha_fin','categoria']
            $table->integer('max_items')->default(10);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('visual_slots'); }
};
