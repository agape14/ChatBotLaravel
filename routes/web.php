<?php

use App\Http\Controllers\whatsappController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::post('/webhook/',[whatsappController::class,"escuchar"]);
Route::get('/webhook/',[whatsappController::class,"token"]);
Route::get('/verificarenv', [whatsappController::class, 'verificarEnv']);
