<?php

namespace Database\Seeders;

use App\Models\Client;
use League\Csv\Reader;
use App\Enums\ClientTypeEnum;
use App\Models\ClientCompany;
use App\Models\ClientIndividual;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class PessoasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $path = database_path('seeders/files/pessoas.csv');
        $csv = Reader::createFromPath($path, 'r');
        $csv->setHeaderOffset(0);

        foreach ($csv as $record) {
            $cpf = preg_replace('/[^0-9]/', '', $record['cpf']);

            if (strlen($cpf) === 11) {

                $individual = Client::create([
                    'type' => 1,
                    'document' => $record['cpf'],
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);

                if (empty($record['email'])) {
                    $record['email'] = $individual->id . '@lexify.com.br';
                }

                ClientIndividual::create([
                    'client_id' => $individual->id,
                    'name' => $record['nome'],
                    'phone' => $record['telefone1'],
                    'email' => $record['email'],
                    'is_active' => true,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);
            } elseif (strlen($cpf) === 14) {

                $company = Client::create([
                    'type' => 2,
                    'document' => $record['cpf'],
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);

                ClientCompany::create([
                    'client_id' => $company->id,
                    'company' => $record['nome'],
                    'phone' => $record['telefone1'],
                    'email' => $record['email'],
                    'is_active' => true,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);
            }
        }
    }
}
