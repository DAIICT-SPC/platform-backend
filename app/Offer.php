<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Offer extends Model
{
    protected $table = 'offers';

    protected $fillable = [
      'placement_id',
        'enroll_no',
        'package',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class, 'enroll_no');
    }

    public function placement()
    {

        return $this->belongsTo(PlacementPrimary::class, 'placement_id');

    }
}
