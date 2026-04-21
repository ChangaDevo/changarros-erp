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
        Schema::create('campanas_email', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained('clientes')->onDelete('cascade');
            $table->foreignId('creado_por')->constrained('users');
            $table->string('titulo');
            $table->string('asunto');
            $table->string('remitente_nombre');
            $table->string('remitente_email');
            $table->longText('cuerpo_html');
            $table->enum('estado', ['borrador', 'enviando', 'enviada', 'pausada'])->default('borrador');
            $table->unsignedInteger('total_contactos')->default(0);
            $table->unsignedInteger('total_enviados')->default(0);
            $table->unsignedInteger('total_errores')->default(0);
            $table->timestamp('enviado_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('campanas_email');
    }
};
