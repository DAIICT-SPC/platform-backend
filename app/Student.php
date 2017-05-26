<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{

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
        return $this->belongsToMany(Education::çlass, 'student_education', 'enroll_no','education_id');
    }

}
