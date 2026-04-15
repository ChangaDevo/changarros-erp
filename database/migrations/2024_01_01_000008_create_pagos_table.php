<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pagos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('proyecto_id')->constrained('proyectos')->onDelete('cascade');
            $table->foreignId('entrega_id')->nullable()->constrained('entregas')->nullOnDelete();
            $table->string('concepto');
            $table->decimal('monto', 10, 2);
            $table->enum('estado', ['pendiente', 'pagado', 'vencido', 'cancelado'])->default('pendiente');
            $table->date('fecha_vencimiento')->nullable();
            $table->timestamp('fecha_pago')->nullable();
            $table->string('metodo_pago')->nullable();
            $table->string('referencia_codi')->nullable();
            $table->string('qr_codigo_path')->nullable();
            $table->string('comprobante_path')->nullable();
            $table->text('notas')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pagos');
    }
};
