<?php

namespace App\Jobs;

use App\Helpers\Helper;
use App\Models\ProcessMovement;
use App\Services\CNJ\Procedural\ProceduralService;
use Carbon\Carbon;
use Filament\Notifications\Notification;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Throwable;

class CreateProcessMovement implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 5;

    public $backoff = 5;

    public $timeout = 120;

    public function __construct(protected $detailId, protected $movement)
    {
        //
    }

    public function handle(): void
    {
        $proceduralService = new ProceduralService();
        $procedural_movement_response = $proceduralService->movements()->get($this->movement->movement_code);

        ProcessMovement::create([
            'process_detail_id' => $this->detailId,
            'code' => $this->movement->movement_code,
            'name' => $this->movement->movement_name,
            'description' => Helper::cleanText($procedural_movement_response[0]['glossario']) ?? null,
            'date' => Carbon::parse($this->movement->movement_date),
            'complements' => json_encode($this->movement->complements, true) ?? null,
        ]);
    }

    public function failed(?Throwable $exception): void
    {
        $recipient = auth()->user();

        Notification::make()
            ->title($exception->getMessage())
            ->sendToDatabase($recipient);
    }
}
