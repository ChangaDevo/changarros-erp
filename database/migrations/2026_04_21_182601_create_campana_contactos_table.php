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
        Schema::create('campana_contactos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campana_id')->constrained('campanas_email')->onDelete('cascade');
            $table->string('nombre');
            $table->string('apellido')->nullable();
            $table->string('email');
            $table->string('empresa')->nullable();
            $table->json('datos_extra')->nullable();
            $table->enum('estado', ['pendiente', 'enviado', 'error'])->default('pendiente');
            $table->timestamp('enviado_at')->nullable();
            $table->text('error_mensaje')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('campana_contactos');
    }
};
