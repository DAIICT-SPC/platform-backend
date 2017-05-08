<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use App\Student;

class StudentPreviousEducation extends Model
{
    protected $table = "studentspreviouseducation";

    protected $fillable = ['enroll_no','clg_school','education','grade_percent','start_year','end_year','drive_link'];

    public function student()
    {
        return $this->belongsTo(Student::class,'enroll_no');
    }
}
