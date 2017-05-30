<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PlacementSeason_Company extends Model
{
    protected $table = 'placements_season_company';

    protected $fillable = [
      'placement_season_id',
      'company_id',
    ];
}
