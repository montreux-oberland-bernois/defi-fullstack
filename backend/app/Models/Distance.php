<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Distance extends Model
{
    use HasFactory;

    protected $fillable = [
        'line_name',
        'parent_station_id',
        'child_station_id',
        'distance_km',
    ];

    protected function casts(): array
    {
        return [
            'distance_km' => 'float',
        ];
    }

    /**
     * The parent station (origin).
     */
    public function parentStation(): BelongsTo
    {
        return $this->belongsTo(Station::class, 'parent_station_id');
    }

    /**
     * The child station (destination).
     */
    public function childStation(): BelongsTo
    {
        return $this->belongsTo(Station::class, 'child_station_id');
    }
}
