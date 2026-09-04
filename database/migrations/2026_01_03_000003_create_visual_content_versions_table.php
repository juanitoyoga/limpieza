<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('visual_content_versions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('content_id')->constrained('visual_contents')->cascadeOnDelete();
            $table->integer('version_num')->default(1);
            $table->json('payload'); 
            // payload ejemplo: {titulo, resumen, cuerpo_html, media_url, media_urls[], orden, link_url, categoria, fecha_inicio, fecha_fin}
            $table->foreignId('created_by')->constrained('users');
            $table->enum('accion', ['creacion','edicion','envio_revision','aprobacion','rechazo','publicacion','archivado']);
            $table->text('comentario_supervisor')->nullable();
            $table->json('diff_snapshot')->nullable(); // opcional: antes vs despues
            $table->timestamps();

            $table->unique(['content_id','version_num']);
            $table->index(['content_id','version_num']);
        });
    }
    public function down(): void { Schema::dropIfExists('visual_content_versions'); }
};
