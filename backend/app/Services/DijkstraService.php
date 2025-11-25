<?php

namespace App\Services;

use SplPriorityQueue;

class DijkstraService
{
    /**
     * Find the shortest path between two nodes using Dijkstra's algorithm.
     *
     * @param  array<string, array<string, float>>  $graph  Adjacency list with weights
     * @param  string  $start  Starting node
     * @param  string  $end  Ending node
     * @return array{path: string[], distance: float}|null Returns path and distance, or null if no path exists
     */
    public function findShortestPath(array $graph, string $start, string $end): ?array
    {
        // Validate nodes exist
        if (! isset($graph[$start]) || ! isset($graph[$end])) {
            return null;
        }

        // Same start and end
        if ($start === $end) {
            return [
                'path' => [$start],
                'distance' => 0.0,
            ];
        }

        // Initialize distances with infinity
        $distances = [];
        $previous = [];

        foreach (array_keys($graph) as $node) {
            $distances[$node] = PHP_FLOAT_MAX;
            $previous[$node] = null;
        }

        $distances[$start] = 0.0;

        // Priority queue: [negative distance, node] (negative because PHP's SplPriorityQueue is max-heap)
        $queue = new SplPriorityQueue();
        $queue->setExtractFlags(SplPriorityQueue::EXTR_BOTH);
        $queue->insert($start, 0);

        $visited = [];

        while (! $queue->isEmpty()) {
            $current = $queue->extract();
            $currentNode = $current['data'];
            $currentDistance = -$current['priority'];

            // Skip if already visited
            if (isset($visited[$currentNode])) {
                continue;
            }

            $visited[$currentNode] = true;

            // Found the target
            if ($currentNode === $end) {
                break;
            }

            // Skip if we've found a better path already
            if ($currentDistance > $distances[$currentNode]) {
                continue;
            }

            // Explore neighbors
            foreach ($graph[$currentNode] as $neighbor => $weight) {
                if (isset($visited[$neighbor])) {
                    continue;
                }

                $newDistance = $distances[$currentNode] + $weight;

                if ($newDistance < $distances[$neighbor]) {
                    $distances[$neighbor] = $newDistance;
                    $previous[$neighbor] = $currentNode;
                    // Use negative priority for min-heap behavior
                    $queue->insert($neighbor, -$newDistance);
                }
            }
        }

        // No path found
        if ($distances[$end] === PHP_FLOAT_MAX) {
            return null;
        }

        // Reconstruct path
        $path = [];
        $current = $end;

        while ($current !== null) {
            array_unshift($path, $current);
            $current = $previous[$current];
        }

        return [
            'path' => $path,
            'distance' => round($distances[$end], 2),
        ];
    }
}
