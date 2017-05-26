<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Job_Type extends Model
{
    protected $table = 'job_types';

    protected $fillable = [
        'job_type',
        'duration',
    ];

    public function placements()
    {
        return $this->hasOne(PlacementPrimary::class,'job_type_id');
    }
}
