<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Station;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StationController extends Controller
{
    /**
     * Get all stations.
     * GET /api/v1/stations
     */
    public function index(Request $request): JsonResponse
    {
        $query = Station::query();

        // Optional search
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('short_name', 'ILIKE', "%{$search}%")
                    ->orWhere('long_name', 'ILIKE', "%{$search}%");
            });
        }

        $stations = $query->orderBy('long_name')->get();

        return response()->json([
            'data' => $stations->map(fn ($station) => [
                'id' => $station->short_name,
                'shortName' => $station->short_name,
                'longName' => $station->long_name,
            ]),
        ]);
    }

    /**
     * Get a specific station.
     * GET /api/v1/stations/{id}
     */
    public function show(string $id): JsonResponse
    {
        $station = Station::where('short_name', $id)->first();

        if (! $station) {
            return response()->json([
                'code' => 'STATION_NOT_FOUND',
                'message' => 'Station non trouvée',
            ], 404);
        }

        return response()->json([
            'id' => $station->short_name,
            'shortName' => $station->short_name,
            'longName' => $station->long_name,
        ]);
    }
}
