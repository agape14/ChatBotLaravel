<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatbotUsuario extends Model
{
    use HasFactory;

    protected $fillable = [
        'numero_telefono',
        'nombre',
        'ultima_interaccion',
    ];

    public function mensajes()
    {
        return $this->hasMany(ChatbotMensaje::class);
    }
}
