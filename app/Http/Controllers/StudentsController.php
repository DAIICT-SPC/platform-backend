<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateInternships;
use App\Http\Requests\CreateProjects;
use App\PlacementOpenFor;
use App\PlacementPrimary;
use Illuminate\Http\Request;
use App\Student;
use App\Helper;
use App\Project;
use App\Internship;
use App\StudentEducation;
use App\Http\Requests\CreateStudentEducation;
use Illuminate\Support\Facades\DB;

class StudentsController extends Controller
{

    public function index()             //to show it to admin as the list of students
    {
        $students = Student::all();

        if(!$students){
            return Helper::apiError('No Student Found!',null,404);
        }

        return $students;
    }


    public function show($user_id)                                      //It will find the user based on foreign key user_id in student table
    {
        $user = Student::where('user_id',$user_id)->first();            //first() because only one entry would be there in student for one user

        if(!$user){
            return Helper::apiError('No Student Found!',null,404);
        }

        return $user;
    }

    public function update(Request $request, $user_id)
    {
        $student = Student::where('user_id',$user_id)->first();            //first() because only one entry would be there in student for one user

        if(!$student){
            return Helper::apiError('No Student Found!',null,404);
        }

        $input = $request->only('enroll_no','student_name','category_id','temp_address','perm_address','contact_no','dob','gender','category','enrollment_date', 'cpi','resume_link');

        $input = array_filter($input, function($value){
            return $value != null;
        });

        $student->update($input);

        return $student;
    }

    public function storeProjects(CreateProjects $request, $user_id)        // ** change - check if already entry exist in array - just like done in education
    {

        $student = Student::find($user_id);

        if(!$student){
            Helper::apiError("No Student Found! Can't store Projects",null,404);
        }

        $enroll_no = $student['enroll_no'];

        $project = $request->only('project_name', 'duration','contribution','description','under_professor');

        $project['enroll_no'] = $enroll_no;

        $stored = Project::create($project);

        return $stored;

    }

    public function fetchProjects($user_id)
    {

        $student = Student::find($user_id)->first();

        if(!$student){
            Helper::apiError("No Student Found! Can't fetch Projects",null,404);
        }

        $enroll_no = $student['enroll_no'];

        $projects = Project::where('enroll_no',$enroll_no)->get();

        return $projects;

    }


    public function storeInternship(CreateInternships $request, $user_id)
    {

        $student = Student::find($user_id);

        if(!$student){
            Helper::apiError("No Student Found! Can't store internships",null,404);
        }

        $enroll_no = $student['enroll_no'];

        $internship = $request->only('company_name', 'title','duration','job_profile','description','stipend');

        $internship['enroll_no'] = $enroll_no;

        $stored = Internship::create($internship);

        return $stored;

    }

    public function fetchInternships($user_id)
    {
        $student = Student::find($user_id)->first();

        if(!$student){
            Helper::apiError("No Student Found! Can't fetch Projects",null,404);
        }

        $enroll_no = $student['enroll_no'];

        $internship = Internship::where('enroll_no',$enroll_no)->get();

        return $internship;

    }

    public function storeStudentEducation(CreateStudentEducation $request,$user_id)
    {

        $student = Student::find($user_id);

        if(!$student){
            Helper::apiError("No Student Found! Can't store Education details",null,404);
        }

        $enroll_no = $student['enroll_no'];

        $input = $request->only('education_id','clg_school','cpi','start_year','end_year','drive_link');

        $input['enroll_no'] = $enroll_no;

        $checking_education = StudentEducation::where('enroll_no',$enroll_no)->where('education_id',$input['education_id'])->first();

        if(is_null($checking_education))
        {

            $education = StudentEducation::create($input);

        }

        else
        {

            $education = $checking_education;

        }

        return $education;

    }

    public function fetchEducation($user_id)
    {
        $student = Student::find($user_id)->first();

        if(!$student){
            Helper::apiError("No Student Found! Can't fetch Education details",null,404);
        }

        $enroll_no = $student['enroll_no'];

        $education_details = StudentEducation::where('enroll_no',$enroll_no)->get();

        if(!$education_details)
        {
            Helper::apiError('No Education Details Stored!',null,404);
        }

        return $education_details;

    }

    public function updateEducation(Request $request,$user_id,$education_id)
    {
        $student = Student::find($user_id)->first();

        if(!$student){
            Helper::apiError("No Student Found!",null,404);
        }

        $enroll_no = $student['enroll_no'];

        $education = StudentEducation::where('enroll_no',$enroll_no)->where('education_id',$education_id)->first();

        $input = $request->only('clg_school','cpi','start_year','end_year','drive_link');

        $input = array_filter($input, function($value){
            return $value != null;
        });

        $education->update($input);

        return $education;

    }


    public function updateProject()
    {

    }

    public function updateInternship()
    {

    }

    public function dashboard($user_id)
    {

        $student = Student::where('user_id',$user_id)->first();

        $placements = PlacementPrimary::where('status','application')->get();

        $dashboard[] = null;

        $i = 0;

        foreach ($placements as $placement)
        {

            $openFor = PlacementOpenFor::where('placement_id',$placement['placement_id'])->get();

            foreach ($openFor as $open)
            {

                if( $student['category_id'] == $open['category_id'])
                {

                    $dashboard[$i] = $placement;

                    $i++;

                }

            }

        }

        $dashboard_proper = array_reverse($dashboard);          //reversing because the NEWS FEED inserted recently should be shown first

        return $dashboard_proper;

    }

}
