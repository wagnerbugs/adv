<?php

namespace App\Listeners;

use Filament\Notifications\Notification;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class QueueFinishedInsertProcessListener
{
    public function __construct()
    {
        //
    }

    public function handle(object $event): void
    {


        $recipient = auth()->user();
        Notification::make()
            ->title("A inserção do processo foi realizada com sucesso.")
            ->sendToDatabase($recipient);
    }
}
