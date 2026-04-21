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
        Schema::create('marcas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained('clientes')->onDelete('cascade');
            $table->foreignId('creado_por')->constrained('users');
            $table->string('nombre');
            $table->text('descripcion')->nullable();
            $table->string('tagline')->nullable();
            $table->string('sitio_web')->nullable();
            $table->string('industria')->nullable();
            $table->boolean('acceso_cliente')->default(false);
            $table->string('token_publico', 64)->unique();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('marcas');
    }
};
