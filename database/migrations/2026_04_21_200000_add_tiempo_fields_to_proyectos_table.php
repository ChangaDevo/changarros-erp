<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('proyectos', function (Blueprint $table) {
            $table->decimal('horas_estimadas', 8, 2)->nullable()->after('monto_total')
                  ->comment('Horas presupuestadas para el proyecto');
            $table->decimal('tarifa_hora', 10, 2)->nullable()->after('horas_estimadas')
                  ->comment('Costo por hora (lo que cobras al cliente)');
        });
    }

    public function down(): void
    {
        Schema::table('proyectos', function (Blueprint $table) {
            $table->dropColumn(['horas_estimadas', 'tarifa_hora']);
        });
    }
};
