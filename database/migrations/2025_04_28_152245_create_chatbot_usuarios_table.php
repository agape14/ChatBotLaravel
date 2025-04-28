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
        Schema::create('chatbot_usuarios', function (Blueprint $table) {
            $table->id();
            $table->string('numero_telefono')->unique();
            $table->string('nombre')->nullable();
            $table->timestamp('ultima_interaccion')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chatbot_usuarios');
    }
};
