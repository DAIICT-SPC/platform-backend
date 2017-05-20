<?php

namespace App\Http\Controllers;

use App\Application;
use App\Helper;
use App\Http\Requests\CreateStudentRegistration;
use App\Offer;
use App\PlacementCriteria;
use App\PlacementPrimary;
use App\Student;
use App\StudentEducation;
use Illuminate\Http\Request;

class PlacementApplicationController extends Controller
{

    public function studentRegistration(CreateStudentRegistration $request, $user_id)          //student registering - Application giving layer - have to validate each student if its eligible or not
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

    public function cancelRegistration(Request $request, $user_id)
    {

        $input = $request->only('placement_id');

        if( !$input)
        {

            Helper::apiError("Placement Id not found",null,404);

        }

        $student = Student::where('user_id',$user_id)->first();

        $application = Application::where('enroll_no',$student['enroll_no'])->where('placement_id',$input['placement_id'])->first();

        if(!$application)
        {

            return Helper::apiError("No Application found!",null,404);

        }

        $application->delete();

        return response("",204);

    }


    public function showAllApplications($user_id,$placement_id)           //Searched by Company as who all have registered.. Also Filter according to their CPI
    {

        $applications = Application::where('placement_id',$placement_id)->get();

        $student_detail[] = null;

        $i = 0;

        foreach ($applications as $application)
        {

            $student_primary = Student::find($application['student_id']);

            $enroll_no = $student_primary['enroll_no'];

            $student_education = StudentEducation::where('enroll_no',$enroll_no)->get();

            $student['student_primary'] = $student_primary;

            $student['student_education'] = $student_education;

            $student_detail[$i] = $student;

            $i++;

        }

        return $student_detail;

    }

}