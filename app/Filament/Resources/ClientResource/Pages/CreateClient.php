<?php

namespace App\Filament\Resources\ClientResource\Pages;

use App\Enums\ClientTypeEnum;
use App\Traits\CapitalizeTrait;
use Illuminate\Support\Facades\Log;
use App\Exceptions\CnpjApiException;
use App\Services\CnpjWs\CnpjWsService;
use App\Exceptions\InvalidCpfException;
use Illuminate\Database\Eloquent\Model;
use Filament\Notifications\Notification;
use App\Services\CnpjWs\Entities\Company;
use App\Filament\Resources\ClientResource;
use Filament\Resources\Pages\CreateRecord;
use App\Exceptions\ApiBrasilRequestException;
use App\Services\CnpjWs\Entities\CompanyError;
use App\Exceptions\ApiBrasilErrorResponseException;
use App\Services\ApiBrasil\CPF\ApiBrasilCPFService;
use App\Services\ApiBrasil\CPF\Entities\Individual;

class CreateClient extends CreateRecord
{
    use CapitalizeTrait;

    protected static string $resource = ClientResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        return static::getModel()::create($data);
    }

    protected function afterCreate(): void
    {
        $client = $this->record;

        if ($client->type === ClientTypeEnum::COMPANY) {
            $document = preg_replace('/[^0-9]/', '', $client->document);

            try {
                $service = new CnpjWsService();
                $company = $service->companies()->get($document);

                Log::debug('Company retrieved: ', $company->toArray());

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

                    $client->company()->update([
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
            } catch (CnpjApiException $e) {
                $recipient = auth()->user();
                Notification::make()
                    ->danger()
                    ->title('Erro na API de CNPJ')
                    ->body($e->getMessage())
                    ->sendToDatabase($recipient);
            } catch (\Exception $e) {
                $recipient = auth()->user();
                Notification::make()
                    ->danger()
                    ->title('Erro interno')
                    ->body($e->getMessage())
                    ->sendToDatabase($recipient);
            }
        } elseif ($client->type === ClientTypeEnum::INDIVIDUAL) {
            $document = preg_replace('/[^0-9]/', '', $client->document);

            try {
                $service = new ApiBrasilCPFService();
                $individual = $service->individuals()->get($document);

                Log::debug('Individual retrieved: ', $individual->toArray());

                if ($individual instanceof Individual) {
                    $client->individual()->update([
                        'name' => $individual->name,
                        'gender' => $individual->gender,
                        'birth_date' => $individual->birth_date,
                        'mother_name' => $individual->mother_name,
                    ]);
                }
            } catch (InvalidCpfException $e) {
                $this->notifyUser('Erro na Validação do CPF', $e->getMessage());
            } catch (ApiBrasilErrorResponseException $e) {
                $this->notifyUser('Erro na API de CPF', $e->getMessage());
            } catch (ApiBrasilRequestException $e) {
                $this->notifyUser('Erro de Requisição à API', $e->getMessage());
            } catch (\Exception $e) {
                $this->notifyUser('Erro Interno', $e->getMessage());
            }
        }
    }

    private function notifyUser(string $title, string $message): void
    {
        $recipient = auth()->user();
        Notification::make()
            ->danger()
            ->title($title)
            ->body($message)
            ->sendToDatabase($recipient);
    }
}
