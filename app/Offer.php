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
}
