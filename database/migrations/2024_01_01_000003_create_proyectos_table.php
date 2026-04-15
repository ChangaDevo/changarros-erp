<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('proyectos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained('clientes')->onDelete('cascade');
            $table->string('nombre');
            $table->text('descripcion')->nullable();
            $table->enum('estado', ['cotizando', 'en_desarrollo', 'en_revision', 'aprobado', 'finalizado'])->default('cotizando');
            $table->decimal('monto_total', 10, 2)->nullable();
            $table->date('fecha_inicio')->nullable();
            $table->date('fecha_entrega_estimada')->nullable();
            $table->date('fecha_entrega_real')->nullable();
            $table->text('notas')->nullable();
            $table->foreignId('creado_por')->constrained('users');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('proyectos');
    }
};
