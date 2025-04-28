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
        Schema::create('chatbot_mensajes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chatbot_usuario_id')->constrained('chatbot_usuarios')->onDelete('cascade');
            $table->string('mensaje_id')->nullable(); // ID de WhatsApp
            $table->string('tipo_mensaje');
            $table->text('contenido');
            $table->timestamp('fecha_envio')->nullable();
            $table->boolean('creado_por_chatbot')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chatbot_mensajes');
    }
};
