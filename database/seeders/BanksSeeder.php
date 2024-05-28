<?php

namespace Database\Seeders;

use App\Models\Bank;
use App\Traits\CapitalizeTrait;
use League\Csv\Reader;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class BanksSeeder extends Seeder
{
    use CapitalizeTrait;

    public function run(): void
    {
        $path = database_path('seeders/files/banks.csv');
        if (!File::exists($path)) {
            $this->command->error("Arquivo CSV não encontrado em {$path}");
            return;
        }
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
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
