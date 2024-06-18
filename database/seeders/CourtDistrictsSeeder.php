<?php

namespace Database\Seeders;

use App\Models\CourtDistrict;
use App\Traits\CapitalizeTrait;
use Illuminate\Database\Seeder;
use League\Csv\Reader;

class CourtDistrictsSeeder extends Seeder
{
    use CapitalizeTrait;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $path = database_path('seeders/files/court_district.csv');
        $csv = Reader::createFromPath($path, 'r');
        $csv->setHeaderOffset(0);

        foreach ($csv as $record) {
            CourtDistrict::create([
                'court' => $record['TRIBUNAL'],
                'state' => $record['UF'],
                'city' => $record['MUNICIPIO'],
                'service_number' => $record['SERVENTIA_NUMERO'],
                'service_name' => $record['SERVENTIA_NOME'],
                'district_code' => $record['ORIGEM'],
                'type' => $record['TIPO'],
                'unit' => $record['UNIDADE'],
                'phone' => $record['TELEFONE'],
                'email' => $record['EMAIL'],
                'address' => $record['ENDERECO'],
                'latitude' => $record['LATITUDE'],
                'longitude' => $record['LONGETUDE'],
                'is_active' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
