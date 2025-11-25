<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\TrainRoute;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class StatsController extends Controller
{
    /**
     * Get dashboard KPIs.
     * GET /api/v1/stats/dashboard
     */
    public function dashboard(): JsonResponse
    {
        $now = Carbon::now();
        $startOfMonth = $now->copy()->startOfMonth();

        // Total routes this month
        $routesThisMonth = TrainRoute::whereDate('created_at', '>=', $startOfMonth)->count();

        // Total distance this month
        $distanceThisMonth = TrainRoute::whereDate('created_at', '>=', $startOfMonth)
            ->sum('distance_km');

        // Total routes all time
        $totalRoutes = TrainRoute::count();

        // Total distance all time
        $totalDistance = TrainRoute::sum('distance_km');

        // Distribution by analytic code (this month)
        $distribution = TrainRoute::whereDate('created_at', '>=', $startOfMonth)
            ->select('analytic_code')
            ->selectRaw('COUNT(*) as count')
            ->selectRaw('SUM(distance_km) as distance')
            ->groupBy('analytic_code')
            ->get()
            ->map(fn ($row) => [
                'code' => $row->analytic_code,
                'count' => (int) $row->count,
                'distance' => round((float) $row->distance, 2),
            ]);

        return response()->json([
            'month' => $now->format('Y-m'),
            'routesThisMonth' => $routesThisMonth,
            'distanceThisMonth' => round((float) $distanceThisMonth, 2),
            'totalRoutes' => $totalRoutes,
            'totalDistance' => round((float) $totalDistance, 2),
            'distribution' => $distribution,
        ]);
    }

    /**
     * Get aggregated distances by analytic code.
     * GET /api/v1/stats/distances
     */
    public function distances(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'from' => 'nullable|date|date_format:Y-m-d',
            'to' => 'nullable|date|date_format:Y-m-d',
            'groupBy' => 'nullable|in:day,month,year,none',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'code' => 'VALIDATION_ERROR',
                'message' => 'Paramètres invalides',
                'details' => $validator->errors()->all(),
            ], 400);
        }

        $from = $request->input('from');
        $to = $request->input('to');
        $groupBy = $request->input('groupBy', 'none');

        // Validate date range
        if ($from && $to && Carbon::parse($from)->gt(Carbon::parse($to))) {
            return response()->json([
                'code' => 'INVALID_DATE_RANGE',
                'message' => 'La date de début doit être antérieure à la date de fin',
            ], 400);
        }

        $query = TrainRoute::query();

        // Apply date filters
        if ($from) {
            $query->whereDate('created_at', '>=', $from);
        }

        if ($to) {
            $query->whereDate('created_at', '<=', $to);
        }

        $items = $this->getAggregatedData($query, $groupBy, $from, $to);

        return response()->json([
            'from' => $from,
            'to' => $to,
            'groupBy' => $groupBy,
            'items' => $items,
        ]);
    }

    /**
     * Get aggregated data based on groupBy parameter.
     */
    private function getAggregatedData($query, string $groupBy, ?string $from, ?string $to): array
    {
        if ($groupBy === 'none') {
            $results = (clone $query)
                ->select('analytic_code')
                ->selectRaw('SUM(distance_km) as total_distance_km')
                ->selectRaw('MIN(created_at) as period_start')
                ->selectRaw('MAX(created_at) as period_end')
                ->groupBy('analytic_code')
                ->get();

            return $results->map(function ($row) {
                return [
                    'analyticCode' => $row->analytic_code,
                    'totalDistanceKm' => round((float) $row->total_distance_km, 2),
                    'periodStart' => $row->period_start ? Carbon::parse($row->period_start)->format('Y-m-d') : null,
                    'periodEnd' => $row->period_end ? Carbon::parse($row->period_end)->format('Y-m-d') : null,
                ];
            })->values()->all();
        }

        $dateFormat = match ($groupBy) {
            'day' => 'Y-m-d',
            'month' => 'Y-m',
            'year' => 'Y',
            default => 'Y-m-d',
        };

        // PostgreSQL date formatting
        $pgFormat = match ($groupBy) {
            'day' => 'YYYY-MM-DD',
            'month' => 'YYYY-MM',
            'year' => 'YYYY',
            default => 'YYYY-MM-DD',
        };

        $results = (clone $query)
            ->select('analytic_code')
            ->selectRaw("TO_CHAR(created_at, '{$pgFormat}') as period_group")
            ->selectRaw('SUM(distance_km) as total_distance_km')
            ->selectRaw('MIN(created_at) as period_start')
            ->selectRaw('MAX(created_at) as period_end')
            ->groupBy('analytic_code', DB::raw("TO_CHAR(created_at, '{$pgFormat}')"))
            ->orderBy('analytic_code')
            ->orderBy('period_group')
            ->get();

        return $results->map(function ($row) {
            return [
                'analyticCode' => $row->analytic_code,
                'totalDistanceKm' => round((float) $row->total_distance_km, 2),
                'periodStart' => $row->period_start ? Carbon::parse($row->period_start)->format('Y-m-d') : null,
                'periodEnd' => $row->period_end ? Carbon::parse($row->period_end)->format('Y-m-d') : null,
                'group' => $row->period_group,
            ];
        })->values()->all();
    }
}
