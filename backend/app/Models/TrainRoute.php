<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TrainRoute extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'train_routes';

    protected $fillable = [
        'from_station_id',
        'to_station_id',
        'analytic_code',
        'distance_km',
        'path',
        'user_id',
    ];

    protected function casts(): array
    {
        return [
            'distance_km' => 'float',
            'path' => 'array',
        ];
    }

    /**
     * The starting station.
     */
    public function fromStation(): BelongsTo
    {
        return $this->belongsTo(Station::class, 'from_station_id');
    }

    /**
     * The destination station.
     */
    public function toStation(): BelongsTo
    {
        return $this->belongsTo(Station::class, 'to_station_id');
    }

    /**
     * The user who created the route.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the route data formatted for API response.
     */
    public function toApiResponse(): array
    {
        return [
            'id' => $this->id,
            'fromStationId' => $this->fromStation->short_name,
            'toStationId' => $this->toStation->short_name,
            'analyticCode' => $this->analytic_code,
            'distanceKm' => $this->distance_km,
            'path' => $this->path,
            'createdAt' => $this->created_at->toIso8601String(),
        ];
    }
}
