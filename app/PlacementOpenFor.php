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

    public function category()
    {
        return $this->belongsTo(Category::class,'category_id');
    }

    public function placements()
    {
        return $this->belongsTo(PlacementPrimary::class,'placement_id');
    }

}
