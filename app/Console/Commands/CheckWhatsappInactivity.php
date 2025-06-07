<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CheckWhatsappInactivity extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'whatsapp:check-inactivity';
    protected $description = 'Check for inactive WhatsApp interactions and send auto messages';

    /**
     * Execute the console command.
     */
    public function handle()
    {
         $controller = new whatsappController();
        $controller->checkInactiveInteractions();
        $this->info('Inactive interactions checked successfully.');
    }
}
