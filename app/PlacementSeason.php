<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PlacementSeason extends Model
{
    protected $table = 'placements_season';

    protected $fillable = [
        'title',
        'status'
    ];

    public function placement_primary()
    {
        return $this->hasMany(PlacementPrimary::class,'placement_season_id');
    }
}
