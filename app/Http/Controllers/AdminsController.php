<?php

namespace App\Http\Controllers;

use App\Category;
use App\Http\Requests\GetFromToYear;
use App\Offer;
use App\Student;
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

        $user = Admin::where('user_id',$user_id)->first();

        if(!$user){
            Helper::apiError('No such Admin exist!',null,404);
        }

        return $user;

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

        $student = Student::with(['category', 'educations'])->where('enroll_no',$enroll_no)->first();

        if(!$student)
        {

            return Helper::apiError("No Student Found!",null,404);

        }

        return $student;

    }

    public function placementsCompanyWise($company_id)
    {

    }

    public function listOfStudentsPlacedInPlacements($placement_id)
    {

    }

    public function listOfStudentsRegisteredForPlacement($placement_id)
    {

    }

    public function listOfStudentsAppearedForRound($placement_id)       //non true for resume shortlisting
    {

    }

    public function roundWisePlacementDetail($placement_id)
    {

    }

    public function roundWisePlacementDetailDescription($placement_id)
    {

    }

}
