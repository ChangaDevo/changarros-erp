<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('meta_tokens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained()->onDelete('cascade');
            $table->string('nombre'); // Label amigable: "Facebook Empresa Demo"
            $table->enum('plataforma', ['facebook', 'instagram']);
            $table->string('page_id');          // Facebook Page ID
            $table->string('ig_account_id')->nullable(); // Instagram Business Account ID
            $table->text('access_token');       // Encriptado
            $table->boolean('activo')->default(true);
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('ultima_verificacion')->nullable();
            $table->string('estado_verificacion')->nullable(); // ok | error | expirado
            $table->timestamps();
        });

        // Agregar campo de error a publicaciones
        Schema::table('publicaciones', function (Blueprint $table) {
            $table->text('error_publicacion')->nullable()->after('audiencia_sugerida');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('meta_tokens');
        Schema::table('publicaciones', function (Blueprint $table) {
            $table->dropColumn('error_publicacion');
        });
    }
};
