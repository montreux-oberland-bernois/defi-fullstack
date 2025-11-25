<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Station extends Model
{
    use HasFactory;

    protected $fillable = [
        'short_name',
        'long_name',
    ];

    /**
     * Distances where this station is the parent (origin).
     */
    public function distancesAsParent(): HasMany
    {
        return $this->hasMany(Distance::class, 'parent_station_id');
    }

    /**
     * Distances where this station is the child (destination).
     */
    public function distancesAsChild(): HasMany
    {
        return $this->hasMany(Distance::class, 'child_station_id');
    }

    /**
     * Routes starting from this station.
     */
    public function routesFrom(): HasMany
    {
        return $this->hasMany(TrainRoute::class, 'from_station_id');
    }

    /**
     * Routes ending at this station.
     */
    public function routesTo(): HasMany
    {
        return $this->hasMany(TrainRoute::class, 'to_station_id');
    }
}
