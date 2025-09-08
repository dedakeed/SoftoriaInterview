<?php

namespace Database\Seeders;

use App\Models\Location;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LocationsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $csvPath = base_path('database/seeders/data/locations_and_languages_databases_2025_08_05.csv');

        $csvSchema = [];
        $csvArray  = [];
        if (($handle = fopen($csvPath, 'r')) !== false) {
            while (($data = fgetcsv($handle, 10000, ",")) !== false) {

                if (!array_filter($data, fn($v) => $v !== null && $v !== '')) continue;
                $csvSchema[] = $data;
            }
            fclose($handle);

            $csvHeaders = $csvSchema[0];

            $csvHeaders[0] = ltrim($csvHeaders[0] ?? '', "\xEF\xBB\xBF");


            for ($index = 1; $index < count($csvSchema); $index++) {

                $row = $csvSchema[$index];
                if (count($row) < count($csvHeaders)) {
                    $row = array_pad($row, count($csvHeaders), null);
                } elseif (count($row) > count($csvHeaders)) {
                    $row = array_slice($row, 0, count($csvHeaders));
                }
                $rowAssoc = array_combine($csvHeaders, $row);
                // мінімальна валідація ключа
                if (
                    isset($rowAssoc['location_code'], $rowAssoc['language_code']) &&
                    $rowAssoc['location_code'] !== '' &&
                    $rowAssoc['language_code'] !== ''
                ) {
                    $csvArray[] = $rowAssoc; // <-- $rows
                }
            }
        }
        $prepared = array_map(
            fn($r) => Location::factory()->fromCsvRow($r)->make()->getAttributes(),
            $csvArray
        );

        DB::disableQueryLog();
        foreach (array_chunk($prepared, 1000) as $chunk) {
            Location::query()->upsert(
                $chunk,
                ['location_code', 'language_code'],
                [
                    'location_name',
                    'location_code_parent',
                    'country_iso_code',
                    'location_type',
                    'available_sources',
                    'language_name',
                    'keywords',
                    'serps',
                ]
            );
        }
    }
}
