<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Internship extends Model
{
    protected $table = 'internships';

    protected $fillable = [
        'company_name',
        'title' ,
        'duration' ,
        'job_profile' ,
        'description' ,
        'stipend',
        'enroll_no',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class,'enroll_no');
    }
}
