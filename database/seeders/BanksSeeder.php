<?php

namespace Database\Seeders;

use App\Models\Bank;
use App\Traits\CapitalizeTrait;
use Illuminate\Database\Seeder;
use League\Csv\Reader;

class BanksSeeder extends Seeder
{
    use CapitalizeTrait;

    public function run(): void
    {
        $path = database_path('seeders/files/banks.csv');
        $csv = Reader::createFromPath($path, 'r');
        $csv->setHeaderOffset(0);

        foreach ($csv as $record) {
            Bank::create([
                'compe' => $record['COMPE'],
                'ispb' => $record['ISPB'],
                'document' => $record['Document'],
                'long_name' => $this->capitalize($record['LongName']),
                'short_name' => $this->capitalize($record['ShortName']),
                'url' => $record['Url'] ?? null,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
