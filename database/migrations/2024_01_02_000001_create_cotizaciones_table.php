<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('cotizaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained('clientes')->onDelete('cascade');
            $table->foreignId('proyecto_id')->nullable()->constrained('proyectos')->nullOnDelete();
            $table->string('nombre');
            $table->enum('estado', ['borrador', 'enviada', 'vista', 'aprobada', 'rechazada', 'vencida'])->default('borrador');
            $table->decimal('iva_porcentaje', 5, 2)->default(16.00);
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('iva_monto', 12, 2)->default(0);
            $table->decimal('total', 12, 2)->default(0);
            $table->text('notas')->nullable();
            $table->string('token', 64)->unique();
            $table->date('fecha_vencimiento')->nullable();
            $table->timestamp('visto_at')->nullable();
            $table->timestamp('aprobado_at')->nullable();
            $table->string('aprobado_ip')->nullable();
            $table->string('aprobado_nombre')->nullable();
            $table->timestamp('rechazado_at')->nullable();
            $table->text('razon_rechazo')->nullable();
            $table->foreignId('creado_por')->constrained('users');
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('cotizaciones'); }
};
