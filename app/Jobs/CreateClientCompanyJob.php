<?php

namespace App\Jobs;

use App\Models\Client;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use App\Services\CnpjWs\CnpjWsService;
use Illuminate\Queue\SerializesModels;
use Filament\Notifications\Notification;
use Illuminate\Queue\InteractsWithQueue;
use App\Services\CnpjWs\Entities\Company;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Services\CnpjWs\Entities\CompanyError;

class CreateClientCompanyJob implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 1;
    public $backoff = 5;
    public $timeout = 120;

    public function __construct(protected Client $client, protected string $document)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $service = new CnpjWsService();
        $company = $service->companies()->get($this->document);

        if ($company instanceof CompanyError) {
            $recipient = auth()->user();
            Notification::make()
                ->danger()
                ->title($company->title)
                ->body($company->details)
                ->sendToDatabase($recipient);

            return;
        }

        if ($company instanceof Company) {

            $this->client->company()->update([
                'company' => $company->company,
                'fantasy_name' => $company->fantasy_name,
                'share_capital' => $company->share_capital,
                'company_size' => $company->company_size,
                'legal_nature' => $company->legal_nature,
                'type' => $company->type,
                'registration_status' => $company->registration_status,
                'registration_date' => $company->registration_date,
                'activity_start_date' => $company->activity_start_date,
                'main_activity' => $company->main_activity,
                'state_registration' => $company->state_registration,
                'state_registration_location' => $company->state_registration_location,
                'partner_name' => $company->partner_name,
                'partner_type' => $company->partner_type,
                'partner_qualification' => $company->partner_qualification,
                'phone' => $company->phone,
                'email' => $company->email,
                'zipcode' => $company->zipcode,
                'street' => $company->street,
                'number' => $company->number,
                'complement' => $company->complement,
                'neighborhood' => $company->neighborhood,
                'city' => $company->city,
                'state' => $company->state,
            ]);
        }
    }
}
