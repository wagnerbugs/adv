<?php

namespace Database\Seeders;

use App\Models\Occupation;
use League\Csv\Reader;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

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
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
