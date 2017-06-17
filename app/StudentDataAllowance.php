<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StudentDataAllowance extends Model
{

    protected $table = "student_data_allowance";

    protected $fillable = [
       'placement_id',
       'status'
    ];

    public function placements()
    {
        return $this->belongsTo(PlacementPrimary::class,'placement_id');
    }

}
