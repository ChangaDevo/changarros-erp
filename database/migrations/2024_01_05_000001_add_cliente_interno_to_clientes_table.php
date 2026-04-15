<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clientes', function (Blueprint $table) {
            $table->boolean('es_cliente_interno')->default(false)->after('activo');
            $table->unsignedTinyInteger('dias_minimos_publicacion')->default(2)->after('es_cliente_interno');
        });
    }

    public function down(): void
    {
        Schema::table('clientes', function (Blueprint $table) {
            $table->dropColumn(['es_cliente_interno', 'dias_minimos_publicacion']);
        });
    }
};
