<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('archivos_entrega', function (Blueprint $table) {
            $table->id();
            $table->foreignId('entrega_id')->constrained('entregas')->onDelete('cascade');
            $table->string('nombre');
            $table->string('archivo_path');
            $table->string('archivo_nombre_original');
            $table->enum('tipo_archivo', ['pdf', 'imagen', 'video_url', 'video_archivo', 'otro'])->default('otro');
            $table->string('video_url')->nullable();
            $table->unsignedBigInteger('archivo_tamanio')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('archivos_entrega');
    }
};
