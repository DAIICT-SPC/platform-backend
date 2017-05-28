<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Application extends Model
{

    protected $table = 'applications';

    protected $fillable = [
      'placement_id', 'enroll_no',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class, 'enroll_no');
    }

    public function placements()
    {
        return $this->belongsTo(PlacementPrimary::class,'placement_id');
    }

}
