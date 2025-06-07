<?php

namespace App\Console\Commands;

use App\Jobs\SendInactivityMessage;
use Illuminate\Console\Command;

class TestNotificationCommand extends Command
{
     protected $signature = 'queue:test';
    protected $description = 'Envía una notificación de prueba al console';

    public function handle()
    {
        $this->info('🎯 Iniciando prueba del sistema de colas...');

        // 1. Mostrar configuración actual
        $this->line('');
        $this->info('ℹ️ Configuración actual:');
        $this->line('Driver de cola: '.config('queue.default'));
        $this->line('Conexión: '.config('queue.connections.'.config('queue.default').'.connection'));

        // 2. Enviar un job de prueba
        $this->line('');
        $testNumber = '51912345678'; // Número de prueba
        SendInactivityMessage::dispatch($testNumber)
            ->delay(now()->addSeconds(10)); // Delay corto para pruebas

        $this->info('✅ Job enviado a la cola para número: '.$testNumber);
        $this->line('⏳ Programado para ejecutarse en 10 segundos');

        // 3. Mostrar instrucciones para probar
        $this->line('');
        $this->info('🛠️ Para probar la ejecución:');
        $this->line('Ejecuta en otra terminal:');
        $this->comment('php artisan queue:work --once');
        $this->line('O para procesar continuamente:');
        $this->comment('php artisan queue:listen');

        // 4. Verificar si hay jobs pendientes
        $this->line('');
        $pendingJobs = \DB::table('jobs')->count();
        $this->info('📊 Jobs pendientes en la cola: '.$pendingJobs);

        return 0;
    }
}
