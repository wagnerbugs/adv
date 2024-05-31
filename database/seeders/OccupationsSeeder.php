<?php

namespace Database\Seeders;

use App\Models\Occupation;
use Illuminate\Database\Seeder;
use League\Csv\Reader;

class OccupationsSeeder extends Seeder
{
    public function run(): void
    {
        $path = database_path('seeders/files/occupations.csv');
        $csv = Reader::createFromPath($path, 'r');
        $csv->setHeaderOffset(0);

        foreach ($csv as $record) {
            Occupation::create([
                'code' => $record['CODIGO'],
                'description' => $record['TITULO'],
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
