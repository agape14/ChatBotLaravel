<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ChatbotUsuario;
use App\Models\ChatbotMensaje;

class ChatbotDashboardController extends Controller
{
    public function index()
    {
        $usuarios = ChatbotUsuario::withCount('mensajes')
            ->orderByDesc('mensajes_count')
            ->get();

        return view('chatbot.dashboard', compact('usuarios'));
    }

    public function verMensajes(ChatbotUsuario $usuario)
    {
        $mensajes = $usuario->mensajes()->latest()->get();
        return view('chatbot.mensajes', compact('usuario', 'mensajes'));
    }

}
