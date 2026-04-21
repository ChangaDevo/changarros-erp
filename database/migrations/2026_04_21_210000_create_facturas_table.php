<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('facturas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained()->cascadeOnDelete();
            $table->foreignId('proyecto_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('creado_por')->constrained('users');

            $table->string('folio')->unique()->comment('FAC-001, REC-001…');
            $table->string('tipo')->default('factura')->comment('factura | recibo');
            $table->string('estado')->default('borrador')->comment('borrador | enviada | pagada | cancelada');

            $table->date('fecha_emision');
            $table->date('fecha_vencimiento')->nullable();

            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('descuento', 12, 2)->default(0)->comment('Monto de descuento');
            $table->decimal('impuesto_porcentaje', 5, 2)->default(0)->comment('% IVA u otro');
            $table->decimal('impuesto_monto', 12, 2)->default(0);
            $table->decimal('total', 12, 2)->default(0);

            $table->string('moneda', 3)->default('MXN');
            $table->string('metodo_pago')->nullable()->comment('Transferencia, Efectivo…');

            $table->text('notas')->nullable();
            $table->text('condiciones')->nullable()->comment('Términos y condiciones');

            $table->timestamp('enviada_at')->nullable();
            $table->timestamp('pagada_at')->nullable();

            $table->string('token_publico', 40)->unique()->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('facturas');
    }
};
