<?php

namespace App\Http\Controllers;

use App\Application;
use App\Category;
use App\Http\Requests\GetFromToYear;
use App\Offer;
use App\PlacementPrimary;
use App\SelectionRound;
use App\SelectStudentRoundwise;
use App\Student;
use App\StudentEducation;
use App\User;
use Illuminate\Http\Request;

use App\Admin;

use App\Helper;

class AdminsController extends Controller
{

    public function index()
    {

    }

    public function show($user_id)
    {

        $admin = Admin::where('user_id',$user_id)->first();

        if(!$admin){
            Helper::apiError('No such Admin exist!',null,404);
        }


        $user = User::where('id',$user_id)->first;

        if(!$user)
        {

            return Helper::apiError("No Name found",null,404);

        }

        $admin['name'] = $user['name'];

        $admin['email'] = $user['email'];

        return $admin;

    }

    public function update(Request $request, $user_id)
    {

        $user = Admin::where('user_id',$user_id)->first();

        if(!$user){
            Helper::apiError('No such Admin exist!',null,404);
        }

        $input = $request->only('name', 'contact_no', 'position');

        $input = array_filter($input, function($value){
            return $value != null;
        });

        $user->update($input);

        return $user;

    }

    public function externalAllow($enroll_no)
    {
        //directly allow his insertion in application table
    }

    public function listOfStudentsPlaced(GetFromToYear $request)
    {

        $input = $request->only('from_year', 'to_year');

        $from_year = $input['from_year'];

        $converting_date = strtotime($from_year);

        $new_from_year = date('Y',$converting_date);

        $to_year = $input['to_year'];

        $converting_date = strtotime($to_year);

        $new_to_year = date('Y',$converting_date);

        $all_placements = Offer::with(['placement'])->whereYear('created_at','>=',$new_from_year)->whereYear('created_at','<=',$new_to_year)->get();

        return $all_placements;

    }

    public function listOfStudentsPlacedCategoryWise(GetFromToYear $request, $user_id, $category_id)
    {

        $input = $request->only('from_year', 'to_year');

        $from_year = $input['from_year'];

        $converting_date = strtotime($from_year);

        $new_from_year = date('Y',$converting_date);

        $to_year = $input['to_year'];

        $converting_date = strtotime($to_year);

        $new_to_year = date('Y',$converting_date);

        $all_placements = Offer::with(['placement', 'student'])->whereYear('created_at','>=',$new_from_year)->whereYear('created_at','<=',$new_to_year)->get();

        $list = [];

        $i = 0;

        foreach ( $all_placements as $placement)
        {

            if($placement['student']['category_id'] == $category_id)
            {

                $list[$i] = $placement;

                $i++;

            }

        }

        return $list;

    }

    public function studentsUnplaced(GetFromToYear $request)
    {

        $input = $request->only('from_year', 'to_year');

        $from_year = $input['from_year'];

        $converting_date = strtotime($from_year);

        $new_from_year = date('Y',$converting_date);

        $to_year = $input['to_year'];

        $converting_date = strtotime($to_year);

        $new_to_year = date('Y',$converting_date);

        $students = Student::where('category_id','!=',null)->whereYear('created_at','>=',$new_from_year)->whereYear('created_at','<=',$new_to_year)->pluck('enroll_no');

        $student_placed = Offer::whereYear('created_at','>=',$new_from_year)->whereYear('created_at','<=',$new_to_year)->pluck('enroll_no');

        $student_placed_unique = array_unique($student_placed->toArray());

        $unplaced_student = array_diff($students->toArray(),$student_placed_unique);

        return array_values($unplaced_student);

    }

    public function studentsUnplacedCategoryWise(GetFromToYear $request)
    {

        $input = $request->only('from_year', 'to_year');

        $from_year = $input['from_year'];

        $converting_date = strtotime($from_year);

        $new_from_year = date('Y',$converting_date);

        $to_year = $input['to_year'];

        $converting_date = strtotime($to_year);

        $new_to_year = date('Y',$converting_date);

        $category_ids = Category::all()->pluck('id');

        $unplaced_category_wise = [];

        foreach ($category_ids as $category_id)
        {

            $students = Student::where('category_id','=',$category_id)->whereYear('created_at','>=',$new_from_year)->whereYear('created_at','<=',$new_to_year)->pluck('enroll_no');

            $all_placements = Offer::with(['student' ])->whereYear('created_at','>=',$new_from_year)->whereYear('created_at','<=',$new_to_year)->get();

            $list = [];

            foreach ( $all_placements as $placement)
            {

                if($placement['student']['category_id'] == $category_id)
                {

                    array_push($list,$placement['student']['enroll_no']);

                }

            }

            $unplaced_category_wise[$category_id] = array_values( array_diff($students->toArray(),$list) );

        }

        $unplaced_category_wise = array_filter($unplaced_category_wise, function($value){

            return $value != null;

        });

        return $unplaced_category_wise;

    }

    public function studentDetail($user_id, $enroll_no)
    {

        $student = Student::with(['category'])->where('enroll_no',$enroll_no)->first();

        if(!$student)
        {

            return Helper::apiError("No Student Found!",null,404);

        }

        $student['education'] = StudentEducation::where('enroll_no',$enroll_no)->get();

        return $student;

    }

    public function placementDriveByCompany($company_id)
    {

    }

    public function placementsCompanyWise($user_id, $company_id)
    {

        $all_placements = PlacementPrimary::with(['placement_season', 'offers' => function($q){
            $q->where('id','!=',null);
        }])->where('company_id',$company_id)->get();

        $placements_done = [];

        foreach ($all_placements as $placement)
        {

            if(!is_null($placement['offers']) and sizeof($placement['offers'])!=0)
            {
                array_push($placements_done,$placement);
            }

        }

        return $placements_done;

    }

    public function placementDrivesByCompany($user_id, $company_id)
    {

        $all_placements = PlacementPrimary::with(['criterias', 'placement_season','placementSelection', 'jobType'])->where('company_id',$company_id)->where('status','!=','draft')->get();

        if(! $all_placements )
        {
            Helper::apiError("No Placement by this Company!",null,404);
        }

        return $all_placements;

    }

    public function listOfStudentsPlacedInPlacements($user_id, $placement_id)
    {

        $placed_students = Offer::with(['student', 'student.student_education'])->where('placement_id',$placement_id)->get();

        if(!$placed_students)
        {
            Helper::apiError("No Students Placed!",null,404);
        }

        return $placed_students;

    }

    public function listOfStudentsRegisteredForPlacement($user_id, $placement_id)
    {

        $registered_students = Application::with('student', 'student.category', 'student.student_education')->where('placement_id',$placement_id)->get();

        if(!$registered_students)
        {
            return Helper::apiError("No Students Registered!",null,404);
        }

        return $registered_students;

    }

    public function roundWisePlacementDetail($user_id, $placement_id)       //only student list
    {

        $rounds = SelectionRound::where('placement_id',$placement_id)->get();

        $round_detail = [];

        foreach ($rounds as $round)
        {

            $students = SelectStudentRoundwise::where('placement_id',$placement_id)->where('round_no',$round['round_no'])->pluck('enroll_no');

            $round['students'] = $students;

            $round_detail[$round['round_no']] = $round;

        }

        return $round_detail;

    }

    public function externalAllowToStudents(Request $request, $user_id, $placement_id)
    {

        $input = $request->only('enroll_no');

        $input['placement_id'] = $placement_id;

        $enroll_no = $input['enroll_no'];

        $check_in_db = Application::where('placement_id',$placement_id)->where('enroll_no',$enroll_no)->first();

        if($check_in_db != null)
        {

            return $check_in_db;

        }

        $new_application = Application::create($input);

        if(!$new_application)
        {
            return Helper::apiError("Cant Allow Student!",null,404);
        }

        return $new_application;

    }

}
