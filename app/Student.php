<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{

    protected $table = "students";

    protected $fillable = ['enroll_no', 'user_id', 'category_id', 'temp_address', 'perm_address', 'contact_no', 'dob', 'gender', 'resume_link'];

    public function projects()
    {
        return $this->hasMany(Project::class,'enroll_no');
    }

    public function internships()
    {
        return $this->hasMany(Internship::class,'enroll_no');
    }
}
