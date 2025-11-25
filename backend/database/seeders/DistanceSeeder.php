<?php

namespace Database\Seeders;

use App\Models\Distance;
use App\Models\Station;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class DistanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $jsonPath = base_path('../distances.json');

        if (! File::exists($jsonPath)) {
            $this->command->error('Le fichier distances.json n\'existe pas!');

            return;
        }

        $lines = json_decode(File::get($jsonPath), true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->command->error('Erreur de parsing JSON: '.json_last_error_msg());

            return;
        }

        // Cache stations by short_name for better performance
        $stations = Station::all()->keyBy('short_name');

        $totalDistances = 0;

        foreach ($lines as $line) {
            $lineName = $line['name'];
            $distances = $line['distances'];

            $this->command->info("Import de la ligne {$lineName} (".count($distances).' segments)...');

            foreach ($distances as $distanceData) {
                $parentStation = $stations->get($distanceData['parent']);
                $childStation = $stations->get($distanceData['child']);

                if (! $parentStation || ! $childStation) {
                    $this->command->warn(
                        "Station non trouvée: {$distanceData['parent']} ou {$distanceData['child']}"
                    );

                    continue;
                }

                Distance::updateOrCreate(
                    [
                        'parent_station_id' => $parentStation->id,
                        'child_station_id' => $childStation->id,
                        'line_name' => $lineName,
                    ],
                    [
                        'distance_km' => $distanceData['distance'],
                    ]
                );

                $totalDistances++;
            }
        }

        $this->command->info("Total: {$totalDistances} distances importées avec succès!");
    }
}
