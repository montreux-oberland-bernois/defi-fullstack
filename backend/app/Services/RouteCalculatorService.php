<?php

namespace App\Services;

use App\Models\TrainRoute;
use Illuminate\Support\Facades\Auth;

class RouteCalculatorService
{
    public function __construct(
        private StationGraphService $graphService,
        private DijkstraService $dijkstraService
    ) {}

    /**
     * Calculate and optionally persist a route between two stations.
     *
     * @param  string  $fromStationId  Short name of the origin station
     * @param  string  $toStationId  Short name of the destination station
     * @param  string  $analyticCode  Analytic code for the route
     * @param  bool  $persist  Whether to save the route to database
     * @return array{success: bool, data?: array, error?: array}
     */
    public function calculate(
        string $fromStationId,
        string $toStationId,
        string $analyticCode,
        bool $persist = true
    ): array {
        // Validate stations exist
        if (! $this->graphService->stationExists($fromStationId)) {
            return [
                'success' => false,
                'error' => [
                    'code' => 'STATION_NOT_FOUND',
                    'message' => 'Station de départ inconnue',
                    'details' => ["La station '{$fromStationId}' n'existe pas"],
                ],
                'status' => 422,
            ];
        }

        if (! $this->graphService->stationExists($toStationId)) {
            return [
                'success' => false,
                'error' => [
                    'code' => 'STATION_NOT_FOUND',
                    'message' => "Station d'arrivée inconnue",
                    'details' => ["La station '{$toStationId}' n'existe pas"],
                ],
                'status' => 422,
            ];
        }

        // Build graph and find shortest path
        $this->graphService->buildGraph();

        // Create adjacency list for Dijkstra
        $graph = [];
        foreach ($this->graphService->getAllStations() as $station) {
            $graph[$station] = $this->graphService->getNeighbors($station);
        }

        $result = $this->dijkstraService->findShortestPath($graph, $fromStationId, $toStationId);

        if ($result === null) {
            return [
                'success' => false,
                'error' => [
                    'code' => 'NO_ROUTE',
                    'message' => 'Aucun itinéraire trouvé',
                    'details' => ["Aucun chemin n'existe entre '{$fromStationId}' et '{$toStationId}'"],
                ],
                'status' => 422,
            ];
        }

        // Prepare route data
        $routeData = [
            'from_station_id' => $this->graphService->getStationId($fromStationId),
            'to_station_id' => $this->graphService->getStationId($toStationId),
            'analytic_code' => $analyticCode,
            'distance_km' => $result['distance'],
            'path' => $result['path'],
            'user_id' => Auth::id(),
        ];

        if ($persist) {
            $route = TrainRoute::create($routeData);

            return [
                'success' => true,
                'data' => $route->toApiResponse(),
            ];
        }

        // Return without persisting
        return [
            'success' => true,
            'data' => [
                'fromStationId' => $fromStationId,
                'toStationId' => $toStationId,
                'analyticCode' => $analyticCode,
                'distanceKm' => $result['distance'],
                'path' => $result['path'],
            ],
        ];
    }
}
