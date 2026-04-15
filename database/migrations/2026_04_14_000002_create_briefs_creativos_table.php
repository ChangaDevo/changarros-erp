<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('briefs_creativos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('proyecto_id')->unique()->constrained('proyectos')->onDelete('cascade');
            $table->text('objetivo_campana')->nullable();
            $table->text('publico_objetivo')->nullable();
            $table->string('tono_voz')->nullable();
            $table->string('colores_marca')->nullable();
            $table->text('competencia')->nullable();
            $table->text('referencias')->nullable();
            $table->text('entregables_esperados')->nullable();
            $table->decimal('presupuesto_referencial', 12, 2)->nullable();
            $table->text('observaciones')->nullable();
            $table->foreignId('creado_por')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('actualizado_por')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('briefs_creativos');
    }
};
