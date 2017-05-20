<?php

namespace App\Http\Controllers;

use App\Application;
use App\Http\Requests\CreateInternships;
use App\Http\Requests\CreateProjects;
use App\Http\Requests\CreateStudentRegistration;
use App\Offer;
use App\PlacementCriteria;
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
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Storage;

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

        $placements = DB::table('placements_primary')
            ->join('placements_open_for', 'placements_primary.placement_id', '=', 'placements_open_for.placement_id')
            ->where('status','application')->get();

        $placements_array = json_decode(json_encode($placements), true);

        $student = Student::where('user_id',$user_id)->first();

        $dashboard[] = null;

        $i = 0;

        foreach ($placements_array as $placement)
        {

                if( $student['category_id'] == $placement['category_id'])
                {

                    $dashboard[$i] = $placement;

                    $i++;

                }

        }

        $dashboard_proper = array_reverse($dashboard);          //reversing because the NEWS FEED inserted recently should be shown first

        return $dashboard_proper;

    }



    public function uploadResume(Request $request, $user_id)
    {

        $student = Student::where('user_id',$user_id)->first();

        if(!$student)
        {

            return Helper::apiError("No student found!",null,404);

        }

        $enroll_no = $student['enroll_no'];

        $inputfile = $request->file('resume');

        if($inputfile==null) {

            return Helper::apiError('File not uploaded', null, 404);

        }

        if ( $inputfile->getClientOriginalExtension() == 'pdf' )
        {

            Storage::put("resume/$enroll_no", $inputfile);

            return response("",204);

        }

        return Helper::apiError("Resume should always be in pdf format",null,404);

    }

    public function getResume($user_id, $student_id = null)
    {

        if( is_null($student_id))
        {

            $student = Student::where('user_id',$user_id)->first();

            if(!$student)
            {

                return Helper::apiError("No student found!",null,404);

            }

            $enroll_no = $student['enroll_no'];

            return Storage::url("resume/$enroll_no");

        }else{

            if(sizeof($student_id)<8)
            {

                $student = Student::where('id',$student_id)->first();

                if(!$student)
                {

                    return Helper::apiError("No student found!",null,404);

                }

                $enroll_no = $student['enroll_no'];

                return base_path().Storage::url("resume/$enroll_no");

            }

            $enroll_no = $student_id;

            return Storage::url("resume/$enroll_no");

        }

    }


    public function eligibility(CreateStudentRegistration $request, $user_id)          //student registering - Application giving layer - have to validate each student if its eligible or not
    {

        $student = Student::where('user_id',$user_id)->first();

        if(!$student)
        {
            Helper::apiError('No such Student Exist!',null,404);
        }

        $student_category = $student['category_id'];

        $enroll_no = $student['enroll_no'];

        $input = $request->only('placement_id');

        $input['enroll_no'] = $enroll_no;

        $placement_id = $request->only('placement_id');


        $application = Application::where('placement_id',$placement_id)->where('enroll_no',$enroll_no)->first();

        if( !is_null($application) )
        {

            return $application;

        }

        $criterias = PlacementCriteria::where('placement_id',$placement_id)->where('category_id',$student_category)->get();

        $student_education_list = StudentEducation::where('enroll_no',$enroll_no)->get();

        $i = 0; $j = 0;

        foreach ($criterias as $criteria)
        {

            $i++;

            foreach ($student_education_list as $student_education)
            {

                if($student_education['education_id'] == $criteria['education_id'])
                {

                    if($student_education['cpi'] >= $criteria['cpi_required'])
                    {
                        $j++;
                    }

                }

            }

        }

        $offer = Offer::where('enroll_no',$enroll_no)->get();

        if( sizeof($offer) > 1 )
        {

            return response("Not Eligible",402);

        }else{

            $offer = Offer::where('enroll_no',$enroll_no)->first();

            $salary = $offer['package'];

            $placement_primary = PlacementPrimary::where('placement_id',$placement_id)->first();

            $package_to_be_given = $placement_primary['package'];

            if( $salary * 1.5 > $package_to_be_given )
            {

                return response("Not Eligible",402);

            }

        }

        if($i == $j)
        {

            return response("Eligible",200);

        }else{

            return response("Not Eligible",402);
        }

    }

    public function capablePlacementList($user_id)
    {

        $student = Student::where('user_id',$user_id)->first();

        if( !$student )
        {

            return Helper::apiError("No Student Found!",null,404);

        }

        $placements = DB::table('placements_primary')
            ->leftjoin('placements_open_for', 'placements_primary.placement_id', '=', 'placements_open_for.placement_id')
            ->leftjoin('placement_criterias', 'placements_primary.placement_id', '=', 'placement_criterias.placement_id')
            ->where('status','application')->where('placements_open_for.category_id',$student['category_id'])->get();

        $placements_array = json_decode(json_encode($placements), true);

        $student_education_list = StudentEducation::where('enroll_no',$student['enroll_no'])->get();

        $placement_capable = [];

        $i = 0;

        foreach ( $placements_array as $placement )
        {

            foreach ( $student_education_list as $student_education )
            {

                if( $student_education['education_id'] == $placement['education_id'] )
                {

                    if()

                }

            }
        }

    }

}
