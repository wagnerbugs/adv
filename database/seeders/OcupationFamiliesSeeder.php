<?php

namespace Database\Seeders;

use App\Models\OcupationFamily;
use League\Csv\Reader;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class OcupationFamiliesSeeder extends Seeder
{
    public function run(): void
    {
        $path = database_path('seeders/files/ocupation-families.csv');
        $csv = Reader::createFromPath($path, 'r');
        $csv->setHeaderOffset(0);

        foreach ($csv as $record) {
            OcupationFamily::create([
                'code' => $record['CODIGO'],
                'description' => $record['TITULO'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
