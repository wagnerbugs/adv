<?php

namespace Database\Seeders;

use App\Models\StateCourt;
use League\Csv\Reader;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class StateCourtsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $path = database_path('seeders/files/state-courts.csv');
        $csv = Reader::createFromPath($path, 'r');
        $csv->setHeaderOffset(0);

        foreach ($csv as $record) {
            StateCourt::create([
                'code' => $record['CODIGO'],
                'abbreviation' => $record['SIGLA'],
                'description' => $record['DESCRICAO'],
                'url' => $record['URL'],
                'is_active' => $record['SITUACAO'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
