<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendInactivityMessage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $phoneNumber;

    /**
     * Create a new job instance.
     */
    public function __construct($phoneNumber)
    {
        $this->phoneNumber = $phoneNumber;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        //$controller = new whatsappController();
        //$controller->checkInactiveInteractions();

         $message = "🚀 Job ejecutado para número: {$this->phoneNumber} a las ".now();

        // Mostrar en consola
        logger()->channel('stdout')->info($message);

        // Registrar en archivo de log
        Log::info($message);
    }
}
