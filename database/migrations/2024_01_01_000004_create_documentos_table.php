<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('documentos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('proyecto_id')->constrained('proyectos')->onDelete('cascade');
            $table->string('nombre');
            $table->enum('tipo', ['contrato', 'cotizacion', 'avance', 'entrega', 'otro'])->default('otro');
            $table->string('archivo_path');
            $table->string('archivo_nombre_original');
            $table->string('archivo_mime')->nullable();
            $table->unsignedBigInteger('archivo_tamanio')->nullable();
            $table->enum('estado', ['borrador', 'enviado', 'aprobado', 'sellado'])->default('borrador');
            $table->boolean('es_sellado')->default(false);
            $table->timestamp('sellado_at')->nullable();
            $table->foreignId('sellado_por')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('subido_por')->constrained('users');
            $table->boolean('visible_cliente')->default(false);
            $table->text('notas')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documentos');
    }
};
