<?php

namespace Tests\Feature;

use App\Models\Distance;
use App\Models\Station;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class RouteApiTest extends TestCase
{
    use RefreshDatabase;

    private string $token;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        // Create test user
        $this->user = User::factory()->create();
        $this->token = JWTAuth::fromUser($this->user);

        // Create test stations
        $this->createTestStations();
    }

    private function createTestStations(): void
    {
        $stations = [
            ['short_name' => 'MX', 'long_name' => 'Montreux'],
            ['short_name' => 'CGE', 'long_name' => 'Montreux-Collège'],
            ['short_name' => 'VUAR', 'long_name' => 'Vuarennes'],
            ['short_name' => 'ZW', 'long_name' => 'Zweisimmen'],
        ];

        foreach ($stations as $station) {
            Station::create($station);
        }

        // Create distances
        $mx = Station::where('short_name', 'MX')->first();
        $cge = Station::where('short_name', 'CGE')->first();
        $vuar = Station::where('short_name', 'VUAR')->first();

        Distance::create([
            'line_name' => 'MOB',
            'parent_station_id' => $mx->id,
            'child_station_id' => $cge->id,
            'distance_km' => 0.65,
        ]);

        Distance::create([
            'line_name' => 'MOB',
            'parent_station_id' => $cge->id,
            'child_station_id' => $vuar->id,
            'distance_km' => 0.35,
        ]);
    }

    public function test_calculate_route_requires_authentication(): void
    {
        $response = $this->postJson('/api/v1/routes', [
            'fromStationId' => 'MX',
            'toStationId' => 'VUAR',
            'analyticCode' => 'TEST-001',
        ]);

        $response->assertStatus(401);
    }

    public function test_calculate_route_with_valid_stations(): void
    {
        $response = $this->withHeaders([
            'Authorization' => "Bearer {$this->token}",
        ])->postJson('/api/v1/routes', [
            'fromStationId' => 'MX',
            'toStationId' => 'VUAR',
            'analyticCode' => 'TEST-001',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'id',
                'fromStationId',
                'toStationId',
                'analyticCode',
                'distanceKm',
                'path',
                'createdAt',
            ]);

        $this->assertEquals('MX', $response->json('fromStationId'));
        $this->assertEquals('VUAR', $response->json('toStationId'));
        $this->assertEquals(['MX', 'CGE', 'VUAR'], $response->json('path'));
        $this->assertEquals(1.0, $response->json('distanceKm'));
    }

    public function test_calculate_route_with_unknown_station(): void
    {
        $response = $this->withHeaders([
            'Authorization' => "Bearer {$this->token}",
        ])->postJson('/api/v1/routes', [
            'fromStationId' => 'UNKNOWN',
            'toStationId' => 'VUAR',
            'analyticCode' => 'TEST-001',
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'code' => 'STATION_NOT_FOUND',
            ]);
    }

    public function test_calculate_route_with_no_path(): void
    {
        $response = $this->withHeaders([
            'Authorization' => "Bearer {$this->token}",
        ])->postJson('/api/v1/routes', [
            'fromStationId' => 'MX',
            'toStationId' => 'ZW',
            'analyticCode' => 'TEST-001',
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'code' => 'NO_ROUTE',
            ]);
    }

    public function test_calculate_route_validates_required_fields(): void
    {
        $response = $this->withHeaders([
            'Authorization' => "Bearer {$this->token}",
        ])->postJson('/api/v1/routes', []);

        $response->assertStatus(400)
            ->assertJson([
                'code' => 'VALIDATION_ERROR',
            ]);
    }

    public function test_calculate_route_accepts_valid_analytic_codes(): void
    {
        $validCodes = ['FRET', 'PASS', 'MAINT', 'TEST'];

        foreach ($validCodes as $code) {
            $response = $this->withHeaders([
                'Authorization' => "Bearer {$this->token}",
            ])->postJson('/api/v1/routes', [
                'fromStationId' => 'MX',
                'toStationId' => 'VUAR',
                'analyticCode' => $code,
            ]);

            $response->assertStatus(201);
            $this->assertEquals($code, $response->json('analyticCode'));
        }
    }

    public function test_calculate_route_rejects_invalid_analytic_code(): void
    {
        $response = $this->withHeaders([
            'Authorization' => "Bearer {$this->token}",
        ])->postJson('/api/v1/routes', [
            'fromStationId' => 'MX',
            'toStationId' => 'VUAR',
            'analyticCode' => 'INVALID-CODE',
        ]);

        $response->assertStatus(400)
            ->assertJson([
                'code' => 'VALIDATION_ERROR',
            ]);
    }

    public function test_get_routes_with_analytic_code_filter(): void
    {
        // Create routes with different analytic codes
        $this->withHeaders([
            'Authorization' => "Bearer {$this->token}",
        ])->postJson('/api/v1/routes', [
            'fromStationId' => 'MX',
            'toStationId' => 'VUAR',
            'analyticCode' => 'FRET',
        ]);

        $this->withHeaders([
            'Authorization' => "Bearer {$this->token}",
        ])->postJson('/api/v1/routes', [
            'fromStationId' => 'MX',
            'toStationId' => 'CGE',
            'analyticCode' => 'PASS',
        ]);

        // Filter by FRET
        $response = $this->withHeaders([
            'Authorization' => "Bearer {$this->token}",
        ])->getJson('/api/v1/routes?analytic_code=FRET');

        $response->assertStatus(200);
        $this->assertEquals(1, $response->json('meta.total'));
        $this->assertEquals('FRET', $response->json('data.0.analyticCode'));
    }

    public function test_get_routes_without_filter_returns_all(): void
    {
        // Create routes
        $this->withHeaders([
            'Authorization' => "Bearer {$this->token}",
        ])->postJson('/api/v1/routes', [
            'fromStationId' => 'MX',
            'toStationId' => 'VUAR',
            'analyticCode' => 'FRET',
        ]);

        $this->withHeaders([
            'Authorization' => "Bearer {$this->token}",
        ])->postJson('/api/v1/routes', [
            'fromStationId' => 'MX',
            'toStationId' => 'CGE',
            'analyticCode' => 'PASS',
        ]);

        $response = $this->withHeaders([
            'Authorization' => "Bearer {$this->token}",
        ])->getJson('/api/v1/routes');

        $response->assertStatus(200);
        $this->assertEquals(2, $response->json('meta.total'));
    }
}
