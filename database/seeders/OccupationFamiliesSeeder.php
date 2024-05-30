<?php

namespace Database\Seeders;

use App\Models\OccupationFamily;
use League\Csv\Reader;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class OccupationFamiliesSeeder extends Seeder
{
    public function run(): void
    {
        $path = database_path('seeders/files/occupation-families.csv');
        $csv = Reader::createFromPath($path, 'r');
        $csv->setHeaderOffset(0);

        foreach ($csv as $record) {
            OccupationFamily::create([
                'code' => $record['CODIGO'],
                'description' => $record['TITULO'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
