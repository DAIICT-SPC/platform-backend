<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PlacementCriteria extends Model
{
    protected $table = 'placement_criterias';

    protected $fillable = [
        'placement_id',
        'education_id',
        'cpi_required',
        'grade_required',
    ];

}
