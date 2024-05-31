<?php

namespace Database\Seeders;

use App\Models\OccupationFamily;
use Illuminate\Database\Seeder;
use League\Csv\Reader;

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
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
