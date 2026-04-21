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
        Schema::create('marca_recursos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('marca_id')->constrained('marcas')->onDelete('cascade');
            $table->enum('tipo', ['logo', 'tipografia', 'color', 'template', 'otro']);
            $table->string('nombre');
            $table->text('descripcion')->nullable();
            $table->string('archivo_path')->nullable();
            $table->string('archivo_nombre_original')->nullable();
            $table->string('archivo_mime')->nullable();
            $table->unsignedBigInteger('archivo_tamanio')->nullable();
            // Solo para tipo=color
            $table->string('color_hex', 7)->nullable();
            $table->string('color_nombre')->nullable();
            // Metadatos extra
            $table->string('variante')->nullable(); // ej: "Principal", "Blanco", "Negro", "Negrita"
            $table->integer('orden')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('marca_recursos');
    }
};
