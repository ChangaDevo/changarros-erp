<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tiempo_registros', function (Blueprint $table) {
            $table->id();
            $table->foreignId('proyecto_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('tarea')->comment('Descripción de lo realizado');
            $table->string('tipo')->default('diseño')
                  ->comment('diseño, redaccion, reunion, revision, desarrollo, administracion, otro');
            $table->integer('minutos')->default(0)->comment('Duración en minutos');
            $table->date('fecha');
            $table->boolean('facturable')->default(true);
            $table->text('notas')->nullable();
            // Para timer en vivo
            $table->timestamp('timer_inicio')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tiempo_registros');
    }
};
