<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{

    protected $table = "categories";

    protected $fillable = ['name'];

    public function students()
    {
        return $this->hasMany(Student::class, 'category_id');
    }

    public function placements()
    {
        return $this->belongsToMany(PlacementPrimary::class, 'placements_open_for', 'category_id', 'placement_id');
    }

    public function placementCriterias()
    {
        return $this->hasMany(PlacementCriteria::class, 'category_id');
    }

}
