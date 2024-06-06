<?php

namespace App\Jobs;

use Throwable;
use App\Helpers\Helper;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use App\Models\ProcessSubject;
use Illuminate\Queue\SerializesModels;
use Filament\Notifications\Notification;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Services\CNJ\Procedural\ProceduralService;

class CreateProcessSubject implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 5;
    public $backoff = 5;
    public $timeout = 120;

    public function __construct(protected $detailId, protected $subject)
    {
        //
    }

    public function handle(): void
    {
        $proceduralService = new ProceduralService();
        $procedural_subject_response = $proceduralService->subjects()->get($this->subject->subject_code);

        ProcessSubject::create([
            'process_detail_id' => $this->detailId,
            'code' => $this->subject->subject_code,
            'name' => $this->subject->subject_name,
            'description' => Helper::cleanText($procedural_subject_response[0]['descricao_glossario']),
            'rule' => $procedural_subject_response[0]['norma'],
            'article' => $procedural_subject_response[0]['artigo'],
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
