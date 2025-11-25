<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\CalculateRouteRequest;
use App\Models\TrainRoute;
use App\Services\RouteCalculatorService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RouteController extends Controller
{
    public function __construct(
        private RouteCalculatorService $routeCalculator
    ) {}

    /**
     * Calculate a route between two stations.
     * POST /api/v1/routes
     */
    public function calculate(CalculateRouteRequest $request): JsonResponse
    {
        $result = $this->routeCalculator->calculate(
            fromStationId: $request->validated('fromStationId'),
            toStationId: $request->validated('toStationId'),
            analyticCode: $request->validated('analyticCode'),
            persist: true
        );

        if (! $result['success']) {
            return response()->json($result['error'], $result['status']);
        }

        return response()->json($result['data'], 201);
    }

    /**
     * Get all routes (with pagination and filters).
     * GET /api/v1/routes
     *
     * @queryParam per_page int Items per page (max 100)
     * @queryParam analytic_code string Filter by analytic code
     * @queryParam from string Filter by start date (Y-m-d)
     * @queryParam to string Filter by end date (Y-m-d)
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = min($request->input('per_page', 15), 100);

        $query = TrainRoute::with(['fromStation', 'toStation']);

        // Filter by analytic code
        if ($request->filled('analytic_code')) {
            $query->where('analytic_code', $request->input('analytic_code'));
        }

        // Filter by date range
        if ($request->filled('from')) {
            $query->whereDate('created_at', '>=', $request->input('from'));
        }
        if ($request->filled('to')) {
            $query->whereDate('created_at', '<=', $request->input('to'));
        }

        $routes = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return response()->json([
            'data' => $routes->map(fn ($route) => $route->toApiResponse()),
            'meta' => [
                'current_page' => $routes->currentPage(),
                'last_page' => $routes->lastPage(),
                'per_page' => $routes->perPage(),
                'total' => $routes->total(),
            ],
        ]);
    }

    /**
     * Get a specific route.
     * GET /api/v1/routes/{id}
     */
    public function show(string $id): JsonResponse
    {
        $route = TrainRoute::with(['fromStation', 'toStation'])->find($id);

        if (! $route) {
            return response()->json([
                'code' => 'ROUTE_NOT_FOUND',
                'message' => 'Trajet non trouvé',
            ], 404);
        }

        return response()->json($route->toApiResponse());
    }
}
