<?php

namespace Database\Seeders;

use App\Models\Court;
use League\Csv\Reader;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class CourtsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $path = database_path('seeders/files/courts.csv');
        $csv = Reader::createFromPath($path, 'r');
        $csv->setHeaderOffset(0);

        foreach ($csv as $record) {
            Court::create([
                'code' => $record['CODIGO'],
                'description' => $record['DESCRICAO'],
                'is_active' => $record['SITUACAO'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
