<?php

namespace App\Http\Controllers;

use App\Application;
use App\Category;
use App\ExternalAllowed;
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


        $user = User::where('id',$user_id)->first();

        if(!$user)
        {

            return Helper::apiError("No Name found",null,404);

        }

        $admin['name'] = $user['name'];

        $admin['email'] = $user['email'];

        $admin['profile_picture'] = $user['profile_picture'];

        return $admin;

    }

    public function update(Request $request, $user_id)
    {

        $user = Admin::where('user_id',$user_id)->first();

        if(!$user){
            Helper::apiError('No such Admin exist!',null,404);
        }

        $input = $request->only('contact_no', 'position');

        $input_user = $request->only('name');

        $userr = User::where('id',$user_id)->first();

        if(!$userr)
        {
            return Helper::apiError("Can't fetch user",null,404);
        }

        $input = array_filter($input, function($value){
            return $value != null;
        });

        $input_user = array_filter($input_user, function($value){
            return $value != null;
        });

        $userr->update($input_user);

        $user->update($input);

        return $user;

    }

    public function listOfStudentsPlaced($placement_season_id)
    {

        $placement_detail = PlacementPrimary::with(['company','jobType','placement_season' => function($q) use($placement_season_id){
            $q->where('id',$placement_season_id);
        }])->where('status','!=','draft')->get();

        $placements = $placement_detail->pluck('placement_id');

        if(sizeof($placements)==0)
        {

            return response("No Placements for this season!",200);

        }

        $student_placed_detail = Offer::with(['student','student.user','student.category','placement','placement.company'])->whereIn('placement_id',$placements)->distinct()->get();

        if(sizeof($student_placed_detail)==0)
        {
            return response("No Student got Offer!",200);
        }

        return $student_placed_detail;

    }

    public function listOfStudentsPlacedCategoryWise($user_id, $placement_season_id, $category_id)
    {

        $placement_detail = PlacementPrimary::with(['company','jobType','placement_season' => function($q) use($placement_season_id){
            $q->where('id',$placement_season_id);
        }])->where('status','!=','draft')->get();

        $placements = $placement_detail->pluck('placement_id');

        if(sizeof($placements)==0)
        {

            return response("No Placements for this season!",200);

        }

        $student_placed_detail = Offer::with(['student' => function($q) use($category_id) {
            $q->where('category_id','=',$category_id);
        },'student.user','placement','placement.company'])->whereIn('placement_id',$placements)->distinct()->get();

        if(sizeof($student_placed_detail)==0)
        {
            return response("No Student got Offer!",200);
        }

        $student_placed = [];

        foreach ($student_placed_detail as $student)
        {

            if(!is_null($student['student']))
            {

                array_push($student_placed,$student);

            }

        }

        if(sizeof($student_placed)==0)
        {

            return response("No Student in this category got Placed!",200);

        }

        return $student_placed;

    }

    public function studentsUnplaced($placement_season_id)
    {

        $placement_detail = PlacementPrimary::with(['company','jobType','placement_season' => function($q) use($placement_season_id){
            $q->where('id',$placement_season_id);
        }])->where('status','!=','draft')->pluck('placement_id');

        if(sizeof($placement_detail)==0)
        {

            return response("No Placements for this season!",200);

        }

        $registered = Application::whereIn('placement_id',$placement_detail)->distinct()->pluck('enroll_no');

        if(!$registered)
        {

            return Helper::apiError("Could not fetch registered student list!",null,404);

        }

        if(sizeof($registered)==0)
        {

            return response("No Student Registered for any placement!",200);

        }

        $offered = Offer::whereIn('placement_id',$placement_detail)->distinct()->pluck('enroll_no');

        if(!$offered)
        {

            return Helper::apiError("Could not fetch registered student list!",null,404);

        }

        if(sizeof($offered)==0)
        {

            return response("No Student Registered for any placement!",200);

        }

        $unplaced_enroll = array_values(array_diff($registered->toArray(),$offered->toArray()));

        if(sizeof($unplaced_enroll)==0)
        {

            return response("All Registeredd Student got placement!",200);

        }

        $students = Student::with(['user','category'])->whereIn('enroll_no',$unplaced_enroll)->get();

        if(!$students)
        {

            return Helper::apiError("Could not fetch student detail!",null,404);

        }

        return $students;

    }

    public function studentsUnplacedCategoryWise($placement_season_id, $category_id)
    {



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

        $user_email = User::where('id',$user_id)->pluck('email');

        if(!$user_email)
        {

            return Helper::apiError("Can't allow as can't find my email!",null,404);

        }

        $new_application = Application::create($input);

        if(!$new_application)
        {
            return Helper::apiError("Cant Allow Student!",null,404);
        }

        $input['email'] = $user_email[0];

        $input['enroll_no'] = $enroll_no;

        $input['placement_id'] = $placement_id;

        $ext_alwd = ExternalAllowed::create($input);

        if(!$ext_alwd)
        {

            return Helper::apiError("Can't create entry in External Allowed DB",null,404);

        }

        return $new_application;

    }

}
