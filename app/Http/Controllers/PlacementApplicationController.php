<?php

namespace App\Http\Controllers;

use App\Application;
use App\Company;
use App\Helper;
use App\Http\Requests\CreateStudentRegistration;
use App\Offer;
use App\PlacementCriteria;
use App\PlacementPrimary;
use App\Student;
use App\StudentEducation;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PlacementApplicationController extends Controller
{

    public function studentRegistration(CreateStudentRegistration $request, $user_id = null)          //student registering - Application giving layer - have to validate each student if its eligible or not
    {

        if (is_null($user_id)) {

            $student = request()->user()->student;

        } else {

            $student = User::find($user_id)->student;

        }

        if(!$student){

            return Helper::apiError('No Student Found!',null,404);

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

            return Helper::apiError("You already have done one Split. Cant allow you",null,402);

        }else{

            $offer = Offer::where('enroll_no',$enroll_no)->first();

            $salary = $offer['package'];

            $placement_primary = PlacementPrimary::where('placement_id',$placement_id)->first();

            $package_to_be_given = $placement_primary['package'];

            if( $salary * 1.5 > $package_to_be_given )
            {

                return Helper::apiError("Your already have Offer with a good Package! Sorry, Cant allow You.",null,402);

            }

        }

        if($i == $j)
        {

            $application = Application::create($input);

            return $application;

        }else{

            return Helper::apiError('Sorry Your Application cant be accepted. You are not eligible!',null,402);

        }

    }

    public function cancelRegistration(Request $request, $user_id = null)
    {

        $input = $request->only('placement_id');

        if( !$input)
        {

            Helper::apiError("Placement Id not found",null,404);

        }

        if (is_null($user_id)) {

            $student = request()->user()->student;

        } else {

            $student = User::find($user_id)->student;

        }

        if(!$student){

            return Helper::apiError('No Student Found!',null,404);

        }

        $application = Application::where('enroll_no',$student['enroll_no'])->where('placement_id',$input['placement_id'])->first();

        if(!$application)
        {

            return Helper::apiError("No Application found!",null,404);

        }

        $application->delete();

        return response("",204);

    }


    public function showAllApplications($user_id, $placement_id)           //Searched by Company as who all have registered.. Also Filter according to their CPI
    {

        $identity = User::where('id',$user_id)->first();

        $role =  $identity["role"];

        if( $role == 'company')
        {
            $placements = PlacementPrimary::find($placement_id);

            $company = Company::where('user_id',$user_id)->first();

            if( $company->id != $placements['company_id']){

                return Helper::apiError("Unauthorized access",null,401);

            }

        }

        $applications = Application::with('student', 'student.category', 'student.student_education')->where('placement_id',$placement_id)->get();

        if(!$applications)
        {
            return Helper::apiError("No Applications done by Students!",null,404);
        }

        return $applications;

    }

}
