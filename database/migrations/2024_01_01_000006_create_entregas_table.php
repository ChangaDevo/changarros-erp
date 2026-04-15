<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('entregas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('proyecto_id')->constrained('proyectos')->onDelete('cascade');
            $table->string('titulo');
            $table->text('descripcion')->nullable();
            $table->enum('tipo', ['diseno_inicial', 'avance', 'revision', 'entrega_final'])->default('avance');
            $table->enum('estado', ['pendiente', 'enviado', 'aprobado', 'rechazado', 'cambios_solicitados'])->default('pendiente');
            $table->date('fecha_entrega')->nullable();
            $table->text('notas_cliente')->nullable();
            $table->integer('orden')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('entregas');
    }
};
