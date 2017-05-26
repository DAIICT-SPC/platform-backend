<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SelectStudentRoundwise extends Model
{

    protected $table = 'select_students_roundwise';

    protected $fillable = [
      'placement_id',
        'enroll_no',
        'round_no',
    ];


}
