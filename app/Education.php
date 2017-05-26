<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Education extends Model
{
    protected $table = 'education';

    protected $fillable = [
      'name',
    ];

    public function students()
    {
        return $this->belongsToMany(Student::çlass, 'student_education', 'education_id','enroll_no');
    }

    public function placementCriteria()
    {
        return $this->hasMany(PlacementCriteria::class, 'education_id');
    }
}
