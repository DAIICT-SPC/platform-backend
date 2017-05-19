<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\PlacementPrimary;

class Category extends Model
{

    protected $table = "categories";

    protected $fillable = ['name'];

    public function placements()
    {
        return $this->belongsToMany(PlacementPrimary::class, 'placements_open_for', 'category_id', 'placement_id');
    }

}
