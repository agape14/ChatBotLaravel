<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatbotMensaje extends Model
{
    use HasFactory;

    protected $fillable = [
        'chatbot_usuario_id',
        'mensaje_id',
        'tipo_mensaje',
        'contenido',
        'fecha_envio',
        'creado_por_chatbot',
    ];

    public function chatbotusuario()
    {
        return $this->belongsTo(ChatbotUsuario::class, 'chatbot_usuario_id');
    }
}
