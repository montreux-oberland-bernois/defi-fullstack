<?php

namespace Database\Seeders;

use App\Models\Station;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class StationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $jsonPath = database_path('data/stations.json');

        if (! File::exists($jsonPath)) {
            $this->command->error('Le fichier stations.json n\'existe pas!');

            return;
        }

        $stations = json_decode(File::get($jsonPath), true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->command->error('Erreur de parsing JSON: '.json_last_error_msg());

            return;
        }

        $this->command->info('Import de '.count($stations).' stations...');

        foreach ($stations as $station) {
            Station::updateOrCreate(
                ['short_name' => $station['shortName']],
                ['long_name' => $station['longName']]
            );
        }

        $this->command->info('Stations importées avec succès!');
    }
}
