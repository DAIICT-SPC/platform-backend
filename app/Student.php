<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $primaryKey = 'enroll_no';

    protected $table = "students";

    protected $fillable = ['enroll_no', 'user_id', 'category_id', 'temp_address', 'perm_address', 'contact_no', 'dob', 'gender', 'resume_link'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function applications()
    {
        return $this->hasMany(Application::class, 'enroll_no');
    }

    public function offers()
    {
        return $this->hasMany(Offer::class, 'enroll_no');
    }

    public function educations()
    {
        return $this->belongsToMany(Education::class, 'students_education', 'enroll_no','education_id');
    }

    public function student_education()
    {
        return $this->hasMany(StudentEducation::class,'enroll_no');
    }

}
