<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WhatsappInteraction extends Model
{
    use HasFactory;

    protected $table = 'whatsapp_interactions';

    protected $fillable = [
        'phone_number',
        'last_interaction',
        'auto_message_sent'
    ];

    protected $casts = [
        'last_interaction' => 'datetime',
        'auto_message_sent' => 'boolean'
    ];
}
