<?php

namespace Database\Seeders;

use App\Models\CourtState;
use League\Csv\Reader;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class CourtStatesSeeder extends Seeder
{
    public function run(): void
    {
        $path = database_path('seeders/files/court_states.csv');
        $csv = Reader::createFromPath($path, 'r');
        $csv->setHeaderOffset(0);

        foreach ($csv as $record) {
            CourtState::create([
                'code' => $record['CODIGO'],
                'court' => $record['SIGLA'],
                'state' => $record['UF'],
                'description' => $record['DESCRICAO'],
                'url' => $record['URL'],
                'is_active' => $record['SITUACAO'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
