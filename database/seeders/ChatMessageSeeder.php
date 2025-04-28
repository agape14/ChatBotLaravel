<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ChatbotMensaje;
use App\Models\ChatbotUsuario;

class ChatMessageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = ChatbotUsuario::all();

        foreach ($users as $user) {
            ChatbotMensaje::factory()->count(10)->create([
                'chatbot_usuario_id' => $user->id,
            ]);
        }
    }
}
