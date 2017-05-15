<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Application extends Model
{

    protected $table = 'applications';

    protected $fillable = [
      'placement_id', 'student_id',
    ];

}
