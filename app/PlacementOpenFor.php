<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\PlacementPrimary;

class PlacementOpenFor extends Model
{
    protected $table = 'placements_open_for';

    protected $fillable = [
        'placement_id',
        'category_id',
    ];

}
