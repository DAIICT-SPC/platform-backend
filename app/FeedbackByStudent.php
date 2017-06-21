<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FeedbackByStudent extends Model
{

    protected $table = 'feedback_by_students';

    protected $fillable = [

        'placement_id', 'enroll_no', 'description', 'rating'

    ];

}
