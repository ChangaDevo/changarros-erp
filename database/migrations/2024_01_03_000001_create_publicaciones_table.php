<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('publicaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained()->onDelete('cascade');
            $table->foreignId('proyecto_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->string('red_social'); // instagram, facebook, tiktok, twitter, linkedin, youtube
            $table->string('titulo');
            $table->text('descripcion');
            $table->string('imagen_path')->nullable();
            $table->datetime('fecha_programada');
            $table->string('estado')->default('borrador'); // borrador, propuesto, aprobado, rechazado, publicado
            $table->text('nota_cliente')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('publicaciones');
    }
};
