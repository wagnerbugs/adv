<?php

namespace App\Jobs;

use Carbon\Carbon;
use App\Helpers\Helper;
use App\Models\ProcessDetail;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Bus;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Services\CNJ\Procedural\ProceduralService;

class CreateProcessDetail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(protected $processId, protected $response)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $proceduralService = new ProceduralService();
        $procedural_class_response = $proceduralService->classes()->get($this->response->class_code);

        $process_detail = ProcessDetail::create([
            'process_id' => $this->processId,
            'process_api_id' => $this->response->process_api_id,
            'class_code' => $this->response->class_code,
            'class_name' => $this->response->class_name,
            'class_description' => Helper::cleanText($procedural_class_response[0]['descricao_glossario']),
            'nature' => $procedural_class_response[0]['natureza'],
            'active_pole' => $procedural_class_response[0]['polo_ativo'],
            'passive_pole' => $procedural_class_response[0]['polo_passivo'],
            'rule' => $procedural_class_response[0]['norma'],
            'article' => $procedural_class_response[0]['artigo'],
            'last_modification_date' => Carbon::parse($this->response->last_modification_date),
            'grade' => $this->response->grade,
            'publish_date' => Carbon::parse($this->response->publish_date),
            'movements' => $this->response->movements,
            'secrecy_level' => $this->response->secrecy_level,
            'judging_code' => $this->response->judging_code,
            'judging_name' => $this->response->judging_name,
            'subjects' => $this->response->subjects,
        ]);

        $jobs = [];
        foreach ($this->response->subjects as $subject) {
            $jobs[] = new CreateProcessSubject($process_detail->id, $subject);
        }

        foreach ($this->response->movements as $movement) {
            $jobs[] = new CreateProcessMovement($process_detail->id, $movement);
        }

        Bus::batch($jobs)->name($process_detail->process_api_id)->dispatch();
    }
}
