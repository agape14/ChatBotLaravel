<?php

use App\Http\Controllers\whatsappController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ChatbotDashboardController;

Route::get('/', function () {
    return view('welcome');
});

Route::post('/webhook/',[whatsappController::class,"escuchar"]);
Route::get('/webhook/',[whatsappController::class,"token"]);
Route::get('/verificarenv', [whatsappController::class, 'verificarEnv']);

Route::get('/chatbot/dashboard', [ChatbotDashboardController::class, 'index'])->name('chatbot.dashboard');
Route::get('/chatbot/usuario/{usuario}', [ChatbotDashboardController::class, 'verMensajes'])->name('chatbot.usuario.mensajes');
