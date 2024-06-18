<?php

namespace Database\Seeders;

use App\Models\Court;
use Illuminate\Database\Seeder;
use League\Csv\Reader;

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
