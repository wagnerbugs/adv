<?php

namespace Database\Seeders;

use App\Models\Ocupation;
use League\Csv\Reader;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class OcupationsSeeder extends Seeder
{

    public function run(): void
    {

        $path = database_path('seeders/files/ocupations.csv');
        $csv = Reader::createFromPath($path, 'r');
        $csv->setHeaderOffset(0);

        foreach ($csv as $record) {
            Ocupation::create([
                'code' => $record['CODIGO'],
                'description' => $record['TITULO'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
