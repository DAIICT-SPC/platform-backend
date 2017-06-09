<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ExternalAllowed extends Model
{
    protected $table = 'external_allowed';

    protected $fillable = [
      'placement_id', 'enroll_no', 'user_id',
    ];

}
