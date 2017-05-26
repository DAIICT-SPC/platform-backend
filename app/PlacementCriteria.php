<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PlacementCriteria extends Model
{
    protected $table = 'placement_criterias';

    protected $fillable = [
        'placement_id',
        'education_id',
        'category_id',
        'cpi_required',
        'grade_required',
    ];

    public function placements()
    {
        return $this->belongsTo(PlacementPrimary::class,'placement_id');
    }

    public function education()
    {
        return $this->belongsTo(Education::class,'education_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class,'category_id');
    }

}
