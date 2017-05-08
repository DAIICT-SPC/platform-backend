<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $table = 'projects';

    protected $fillable = [
        'project_name',
        'duration',
        'contribution',
        'description' ,
        'under_professor',
        'enroll_no',
        ];

    public function student()
    {
        return $this->belongsTo(Student::class,'enroll_no');
    }
}
