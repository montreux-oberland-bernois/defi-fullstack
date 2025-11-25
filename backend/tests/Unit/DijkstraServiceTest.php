<?php

namespace Tests\Unit;

use App\Services\DijkstraService;
use PHPUnit\Framework\TestCase;

class DijkstraServiceTest extends TestCase
{
    private DijkstraService $dijkstra;

    protected function setUp(): void
    {
        parent::setUp();
        $this->dijkstra = new DijkstraService();
    }

    public function test_finds_shortest_path_in_simple_graph(): void
    {
        $graph = [
            'A' => ['B' => 1, 'C' => 4],
            'B' => ['A' => 1, 'C' => 2, 'D' => 5],
            'C' => ['A' => 4, 'B' => 2, 'D' => 1],
            'D' => ['B' => 5, 'C' => 1],
        ];

        $result = $this->dijkstra->findShortestPath($graph, 'A', 'D');

        $this->assertNotNull($result);
        $this->assertEquals(['A', 'B', 'C', 'D'], $result['path']);
        $this->assertEquals(4.0, $result['distance']);
    }

    public function test_finds_direct_path(): void
    {
        $graph = [
            'A' => ['B' => 5],
            'B' => ['A' => 5],
        ];

        $result = $this->dijkstra->findShortestPath($graph, 'A', 'B');

        $this->assertNotNull($result);
        $this->assertEquals(['A', 'B'], $result['path']);
        $this->assertEquals(5.0, $result['distance']);
    }

    public function test_returns_null_when_no_path_exists(): void
    {
        $graph = [
            'A' => ['B' => 1],
            'B' => ['A' => 1],
            'C' => ['D' => 1],
            'D' => ['C' => 1],
        ];

        $result = $this->dijkstra->findShortestPath($graph, 'A', 'D');

        $this->assertNull($result);
    }

    public function test_returns_null_when_start_node_does_not_exist(): void
    {
        $graph = [
            'A' => ['B' => 1],
            'B' => ['A' => 1],
        ];

        $result = $this->dijkstra->findShortestPath($graph, 'X', 'B');

        $this->assertNull($result);
    }

    public function test_returns_null_when_end_node_does_not_exist(): void
    {
        $graph = [
            'A' => ['B' => 1],
            'B' => ['A' => 1],
        ];

        $result = $this->dijkstra->findShortestPath($graph, 'A', 'X');

        $this->assertNull($result);
    }

    public function test_same_start_and_end_returns_zero_distance(): void
    {
        $graph = [
            'A' => ['B' => 1],
            'B' => ['A' => 1],
        ];

        $result = $this->dijkstra->findShortestPath($graph, 'A', 'A');

        $this->assertNotNull($result);
        $this->assertEquals(['A'], $result['path']);
        $this->assertEquals(0.0, $result['distance']);
    }

    public function test_finds_shortest_among_multiple_paths(): void
    {
        // A --10-- B
        // |        |
        // 1        1
        // |        |
        // C --1--- D
        $graph = [
            'A' => ['B' => 10, 'C' => 1],
            'B' => ['A' => 10, 'D' => 1],
            'C' => ['A' => 1, 'D' => 1],
            'D' => ['B' => 1, 'C' => 1],
        ];

        $result = $this->dijkstra->findShortestPath($graph, 'A', 'B');

        // Shortest path: A -> C -> D -> B (distance: 3)
        // Not: A -> B (distance: 10)
        $this->assertNotNull($result);
        $this->assertEquals(['A', 'C', 'D', 'B'], $result['path']);
        $this->assertEquals(3.0, $result['distance']);
    }

    public function test_handles_decimal_distances(): void
    {
        $graph = [
            'MX' => ['CGE' => 0.65],
            'CGE' => ['MX' => 0.65, 'VUAR' => 0.35],
            'VUAR' => ['CGE' => 0.35],
        ];

        $result = $this->dijkstra->findShortestPath($graph, 'MX', 'VUAR');

        $this->assertNotNull($result);
        $this->assertEquals(['MX', 'CGE', 'VUAR'], $result['path']);
        $this->assertEquals(1.0, $result['distance']);
    }
}
