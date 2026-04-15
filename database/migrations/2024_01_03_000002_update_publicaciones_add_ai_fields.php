<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('publicaciones', function (Blueprint $table) {
            // Audiencia sugerida por la IA
            $table->text('audiencia_sugerida')->nullable()->after('nota_cliente');
            // Renombrar imagen_path a archivo_path para soportar videos también
            $table->renameColumn('imagen_path', 'archivo_path');
        });
    }

    public function down(): void
    {
        Schema::table('publicaciones', function (Blueprint $table) {
            $table->dropColumn('audiencia_sugerida');
            $table->renameColumn('archivo_path', 'imagen_path');
        });
    }
};
