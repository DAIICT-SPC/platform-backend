<?php

namespace App\Http\Controllers;

use App\Application;
use App\Category;
use App\Company;
use App\ExternalAllowed;
use App\Http\Requests\GetFromToYear;
use App\Offer;
use App\PlacementOpenFor;
use App\PlacementPrimary;
use App\PlacementSeason;
use App\SelectionRound;
use App\SelectStudentRoundwise;
use App\Student;
use App\StudentEducation;
use App\User;
use function foo\func;
use Illuminate\Http\Request;

use App\Admin;

use App\Helper;

class AdminsController extends Controller
{

    public function index()
    {

        $admins = Admin::with(['user'])->get();

        if(!$admins)
        {
            return Helper::apiError("Cant fetch admins",null,404);
        }

        return $admins;

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

        $input_user = $request->only('name','alternate_email');

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

        $placement_detail_list = PlacementPrimary::with(['placement_season' => function($q) use($placement_season_id){
            $q->where('id',$placement_season_id);
        }])->where('status','!=','draft')->get();

        $placements = [];

        foreach ( $placement_detail_list as $placement )
        {

            if(sizeof($placement["placement_season"])!=0)
            {

                array_push($placements, $placement['placement_id']);

            }

        }

        if(sizeof($placements)==0)
        {

            return response("No Placements for this season!",200);

        }

        $student_placed_detail = Offer::with(['student','student.user','student.category','placement','placement.company'])->whereIn('placement_id',$placements)->where('package','!=',0)->distinct()->get();

        if(sizeof($student_placed_detail)==0)
        {
            return response("No Student got Offer!",200);
        }

        return $student_placed_detail;

    }

    public function listOfStudentsPlacedCategoryWise($user_id, $placement_season_id, $category_id)
    {

        $placement_detail_list = PlacementPrimary::with(['placement_season' => function($q) use($placement_season_id){
            $q->where('id',$placement_season_id);
        }])->where('status','!=','draft')->get();

        $placements = [];

        foreach ( $placement_detail_list as $placement )
        {

            if(sizeof($placement["placement_season"])!=0)
            {

                array_push($placements, $placement['placement_id']);

            }

        }

        if(sizeof($placements)==0)
        {

            return response("No Placements for this season!",200);

        }

        $open_for = PlacementOpenFor::whereIn('placement_id',$placements)->where('category_id',$category_id)->pluck('placement_id');

        if(sizeof($open_for)==0)
        {

            return response("Not open for this category",200);

        }

        $student_placed_detail = Offer::with(['student' => function($q) use($category_id){
            $q->where('category_id',$category_id);
        },'student.user','student.category','placement','placement.company'])->whereIn('placement_id',$open_for)->where('package','!=',0)->distinct()->get();

        $student_placed = [];

        foreach ($student_placed_detail as $student_detail)
        {

            if(sizeof($student_detail['student'])!=0)
            {

                array_push($student_placed,$student_detail);

            }

        }

        if(sizeof($student_placed)==0)
        {
            return response("No Student got Offer!",200);
        }

        return $student_placed;

    }

    public function studentsUnplaced($placement_season_id)
    {

        $placement_detail = PlacementPrimary::with(['company','jobType'])->where('placement_season_id',$placement_season_id)->where('status','!=','draft')->pluck('placement_id');

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

        $offered = Offer::whereIn('placement_id',$placement_detail)->where('package','!=',0)->distinct()->pluck('enroll_no');

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

            return response("All Registered Student got placement!",200);

        }

        $students = Student::with(['user','category'])->whereIn('enroll_no',$unplaced_enroll)->get();

        if(!$students)
        {

            return Helper::apiError("Could not fetch student detail!",null,404);

        }

        return $students;

    }

    public function studentsUnplacedCategoryWise($user_id,$placement_season_id, $category_id)
    {


        $placement_listing = PlacementPrimary::where('placement_season_id',$placement_season_id)->where('status','!=','draft')->pluck('placement_id');

        if(!$placement_listing)
        {
            return Helper::apiError("Can't fetch Placement Details!",null,404);
        }

        $placement_list = PlacementOpenFor::whereIn('placement_id',$placement_listing)->where('category_id',$category_id)->distinct()->pluck('placement_id');

        if(sizeof($placement_list)==0)
        {
            return response("No Placement Id found!",200);
        }

        $registered_students = Application::whereIn('placement_id',$placement_list)->distinct()->pluck('enroll_no');

        if(!$registered_students)
        {
            return response("No student registered",200);
        }

        $offered_list = Offer::whereIn('placement_id',$placement_list)->where('package','!=',0)->distinct()->pluck('enroll_no');

        if(sizeof($offered_list)==0)
        {

            $students = Student::with(['user','category'])->whereIn('enroll_no',$registered_students)->where('category_id',$category_id)->get();

            if(sizeof($students)==0)
            {

                return response("No student of this category registered!",200);

            }

            return $students;

        }

        $student_list = array_values(array_diff($registered_students->toArray(),$offered_list->toArray()));

        if(sizeof($student_list)==0)
        {

            return response("All Students Placed!",200);

        }

        $students = Student::with(['user','category'])->whereIn('enroll_no',$student_list)->where('category_id',$category_id)->get();

        if(!$students)
        {

            return Helper::apiError("Could not fetch student detail!",null,404);

        }

        return $students;

    }

    public function externallyAllowed($user_id,$placement_season_id)
    {

        $placement_detail_list = PlacementPrimary::with(['externally_allowed','externally_allowed.externally_allowed_by','placement_season' => function($q) use($placement_season_id){
            $q->where('id',$placement_season_id);
        }])->where('status','!=','draft')->get();

        $placements = [];

        foreach ( $placement_detail_list as $placement )
        {

            if(sizeof($placement["placement_season"])!=0)
            {

                array_push($placements, $placement);

            }

        }

        if(sizeof($placements)==0)
        {

            return response("No Placements for this season!",200);

        }

        return $placements;

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

    public function placementDrivesByCompany($user_id, $company_id)
    {

        $all_placements = PlacementPrimary::with(['criterias', 'placement_season','placementSelection', 'jobType'])->where('company_id',$company_id)->where('status','!=','draft')->get();

        if(! $all_placements )
        {
            Helper::apiError("No Placement by this Company!",null,404);
        }

        return $all_placements;

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

    public function roundWisePlacementDetail($user_id, $placement_id, $round_no)       //only student list
    {

        $round_details = SelectionRound::where('placement_id',$placement_id)->pluck('round_no');

        $size = sizeof($round_details);

        if($size == 0)
        {
            return response("No Selection Round!",200);
        }

        $last_round = $round_details[$size-1];

        if($round_no == $last_round)
        {

            $students_enroll_no = SelectStudentRoundwise::where('placement_id',$placement_id)->where('round_no',$round_no)->pluck('enroll_no');

        }
        else
        {

            $rounds_upto_now = [];

            for($i=1;$i<=$round_no;$i++)
            {

                array_push($rounds_upto_now,$i);

            }

            $round_after_now = array_values(array_diff($round_details->toArray(),$rounds_upto_now));

            if(!in_array($round_no,$round_after_now))
            {

                array_push($round_after_now,$round_no);

            }

            $students_enroll_no = SelectStudentRoundwise::where('placement_id',$placement_id)->whereIn('round_no',$round_after_now)->pluck('enroll_no');

        }

        if(!$students_enroll_no)
        {
            return Helper::apiError("Can't fetch Student Enroll No.",null,404);
        }

        $students = Student::with(['user','category'])->whereIn('enroll_no',$students_enroll_no)->get();

        if(sizeof($students)==0)
        {
            return Helper::apiError("Can't fetch students!",null,404);
        }

        return $students;

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

    public function reportStudentWise($user_id,$placement_season_id,$enroll_no)
    {

        $placement_detail_list = PlacementPrimary::with([ 'placementSelection','company','applications' =>function($q) use($enroll_no){
            $q->where('enroll_no',$enroll_no);
        },'placement_season' => function($q) use($placement_season_id){
            $q->where('id',$placement_season_id);
        }])->where('status','!=','draft')->get();

        $student_staged = [];

        $placement_ids = [];

        foreach ( $placement_detail_list as $placement )
        {

            if(sizeof($placement["placement_season"])!=0 && sizeof($placement['applications'])!=0)
            {

                $student_reached_till = SelectStudentRoundwise::where('placement_id',$placement['placement_id'])->where('enroll_no',$enroll_no)->first();

                $selection_round_detail = SelectionRound::where('placement_id',$placement['placement_id'])->where('round_no',$student_reached_till['round_no'])->first();

                $student_reached_till['round_details'] = $selection_round_detail;

                $placement['student_reached_till_round'] = $student_reached_till;

                array_push($student_staged, $placement);

                array_push($placement_ids,$placement['placement_id']);

            }

        }

        $offer_received = Offer::with(['placement','placement.company'])->whereIn('placement_id',$placement_ids)->where('enroll_no',$enroll_no)->where('package','!=',0)->first();

        if(sizeof($offer_received)!=0)
        {

            return $offer_received;

        }
        else
        {

            return $student_staged;

        }

    }

}
