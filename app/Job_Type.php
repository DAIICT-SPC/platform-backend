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
}
