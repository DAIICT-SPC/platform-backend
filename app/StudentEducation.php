<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StudentEducation extends Model
{

    protected $table = 'students_education';

    protected $fillable = [
        'enroll_no',
        'education_id',
        'clg_school',
        'start_year',
        'end_year',
        'drive_link',
        'cpi',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class,'enroll_no');
    }

    public function education()
    {
        return $this->belongsTo(Education::class,'education_id');
    }

}
