<?php

namespace App\Services;

use App\Models\Distance;
use App\Models\Station;
use Illuminate\Support\Collection;

class StationGraphService
{
    /**
     * @var array<string, array<string, float>>
     */
    private array $graph = [];

    /**
     * @var array<string, int>
     */
    private array $stationIdMap = [];

    private bool $initialized = false;

    /**
     * Build the graph from database distances.
     * The graph is bidirectional (trains can go both ways).
     */
    public function buildGraph(): void
    {
        if ($this->initialized) {
            return;
        }

        // Load all stations
        $stations = Station::all();
        foreach ($stations as $station) {
            $this->stationIdMap[$station->short_name] = $station->id;
            $this->graph[$station->short_name] = [];
        }

        // Load all distances and build bidirectional graph
        $distances = Distance::with(['parentStation', 'childStation'])->get();

        foreach ($distances as $distance) {
            $parent = $distance->parentStation->short_name;
            $child = $distance->childStation->short_name;
            $km = (float) $distance->distance_km;

            // If there's already a distance (from another line), keep the shortest
            if (! isset($this->graph[$parent][$child]) || $this->graph[$parent][$child] > $km) {
                $this->graph[$parent][$child] = $km;
            }

            // Bidirectional: add reverse direction
            if (! isset($this->graph[$child][$parent]) || $this->graph[$child][$parent] > $km) {
                $this->graph[$child][$parent] = $km;
            }
        }

        $this->initialized = true;
    }

    /**
     * Get neighbors of a station with their distances.
     *
     * @return array<string, float>
     */
    public function getNeighbors(string $stationShortName): array
    {
        $this->buildGraph();

        return $this->graph[$stationShortName] ?? [];
    }

    /**
     * Get all station short names.
     *
     * @return string[]
     */
    public function getAllStations(): array
    {
        $this->buildGraph();

        return array_keys($this->graph);
    }

    /**
     * Check if a station exists.
     */
    public function stationExists(string $shortName): bool
    {
        $this->buildGraph();

        return isset($this->graph[$shortName]);
    }

    /**
     * Get station ID by short name.
     */
    public function getStationId(string $shortName): ?int
    {
        $this->buildGraph();

        return $this->stationIdMap[$shortName] ?? null;
    }

    /**
     * Reset the graph (useful for testing).
     */
    public function reset(): void
    {
        $this->graph = [];
        $this->stationIdMap = [];
        $this->initialized = false;
    }
}
