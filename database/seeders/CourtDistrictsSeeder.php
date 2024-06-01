<?php

namespace Database\Seeders;

use App\Models\CourtDistrict;
use App\Traits\CapitalizeTrait;
use League\Csv\Reader;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class CourtDistrictsSeeder extends Seeder
{
    use CapitalizeTrait;
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $path = database_path('seeders/files/court_districts.csv');
        $csv = Reader::createFromPath($path, 'r');
        $csv->setHeaderOffset(0);

        foreach ($csv as $record) {
            CourtDistrict::create([
                'code' => $record['CODIGO'],
                'court' => $record['TRIBUNAL'],
                'district' => $this->capitalize($record['MUNICIPIO']),
                'description' => $this->capitalize($record['NOME']),
                'type' => $this->capitalize($record['TIPO']),
                'classification' => $this->capitalize($record['CLASSIFICACAO']),
                'is_active' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
