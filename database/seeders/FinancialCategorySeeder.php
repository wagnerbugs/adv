<?php

namespace Database\Seeders;

use League\Csv\Reader;
use Illuminate\Database\Seeder;
use App\Models\FinancialCategory;
use App\Enums\TransactionTypeEnum;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class FinancialCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $path_parent = database_path('seeders/files/parents-categories.csv');
        $csv_parent = Reader::createFromPath($path_parent, 'r');
        $csv_parent->setHeaderOffset(0);

        foreach ($csv_parent as $record) {
            FinancialCategory::create([
                'type' => $record['ENUM'],
                'parent_id' => null,
                'name' => $record['NAME'],
                'description' => null,
                'is_active' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }

        $path = database_path('seeders/files/parents-subcategories.csv');
        $csv = Reader::createFromPath($path, 'r');
        $csv->setHeaderOffset(0);

        foreach ($csv as $record) {
            FinancialCategory::create([
                'type' => $record['ENUM'],
                'parent_id' => $record['PARENT_ID'],
                'name' => $record['NAME'],
                'description' => null,
                'is_active' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }
    }
}
