<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateInternships;
use App\Http\Requests\CreatePreviousEducation;
use App\Http\Requests\CreateProjects;
use Illuminate\Http\Request;
use App\Student;
use App\Helper;
use App\StudentPreviousEducation;
use App\Project;
use App\Internship;

class StudentsController extends Controller
{

    public function index()
    {
        //
    }


    public function show($user_id)                                      //It will find the user based on foreign key user_id in student table
    {
        $user = Student::where('user_id',$user_id)->first();            //first() because only one entry would be there in student for one user

        if(!$user){
            return Helper::apiError('No such Entry found for Student!',null,404);
        }

        return $user;
    }

    public function update(Request $request, $user_id)
    {
        $student = Student::where('user_id',$user_id)->first();            //first() because only one entry would be there in student for one user

        if(!$student){
            return Helper::apiError('No such Entry found for Student!',null,404);
        }

        $input = $request->only('enroll_no','student_name','category_id','temp_address','perm_address','contact_no','dob','gender','category','enrollment_date', 'cpi','resume_link');

        $student->update($input);

        return $student;
    }

    public function storePreviousEducation(CreatePreviousEducation $request, $user_id)
    {
        $student = Student::find($user_id);

        if(!$student){
            Helper::apiError("No such Student exist! Can't store Previous Education",null,404);
        }

        $enroll_no = $student['enroll_no'];

        $previousEducation = $request->only('clg_school', 'education','grade','start_year','end_year','drive_link');

        $previousEducation['enroll_no'] = $enroll_no;

        $stored = StudentPreviousEducation::create($previousEducation);

        return $stored;
    }

    public function fetchPreviousEducation($user_id)
    {

        $student = Student::find($user_id)->first();

        if(!$student){
            Helper::apiError("No such Student exist! Can't fetch Previous Education",null,404);
        }

        $enroll_no = $student['enroll_no'];

        $previousEducation = StudentPreviousEducation::where('enroll_no',$enroll_no)->get();

        return $previousEducation;

    }


    public function updatePreviousEducation(CreatePreviousEducation $request, $user_id, $id)
    {
        $student = Student::find($user_id)->first();

        if(!$student){
            Helper::apiError("No such Student exist! Can't store Previous Education",null,404);
        }

        $enroll_no = $student['enroll_no'];

        $previousEducation = StudentPreviousEducation::where('enroll_no',$enroll_no)->get();

        foreach ($previousEducation as $pe){

            if($pe['id'] == $id){

                $pe = $request->only('clg_school', 'education','grade','start_year','end_year','drive_link');

                $input = array_filter($pe, function($value){
                    return $value != null;
                });

                $input->update($input);                 //giving error over here - Call to a member function update() on array

                return $input;
            }

        }

    }

    public function deletePreviousEducation(CreatePreviousEducation $request, $user_id, $id)
    {

    }

    public function storeProjects(CreateProjects $request, $user_id)
    {
        $student = Student::find($user_id);

        if(!$student){
            Helper::apiError("No such Student exist! Can't store Projects",null,404);
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
            Helper::apiError("No such Student exist! Can't fetch Projects",null,404);
        }

        $enroll_no = $student['enroll_no'];

        $projects = Project::where('enroll_no',$enroll_no)->get();

        return $projects;

    }


    public function storeInternship(CreateInternships $request, $user_id)
    {

        $student = Student::find($user_id);

        if(!$student){
            Helper::apiError("No such Student exist! Can't store internships",null,404);
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
            Helper::apiError("No such Student exist! Can't fetch Projects",null,404);
        }

        $enroll_no = $student['enroll_no'];

        $internship = Internship::where('enroll_no',$enroll_no)->get();

        return $internship;

    }

    public function updateProject()
    {

    }

    public function updateInternship()
    {

    }

}
