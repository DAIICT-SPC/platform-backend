<?php

namespace App\Http\Controllers;

use App\Company;
use App\Feedback;
use App\FeedbackByStudent;
use App\Helper;
use App\Http\Requests\CreateFeedback;
use App\Mail\FeedbackEmail;
use App\PlacementPrimary;
use App\Student;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class FeedbackController extends Controller
{

    public function isFeedbackGiven($user_id, $placement_id)
    {

        $check_in_db = Feedback::where('placement_id',$placement_id)->first();

        if(sizeof($check_in_db)!=0)
        {

            return "true";

        }
        else{

            return "false";

        }


    }


    public function isFeedbackGivenByStudent($user_id, $placement_id)
    {

        if (is_null($user_id)) {

            $student = request()->user()->student;

        } else {

            $student = User::find($user_id)->student;

        }

        $enroll_no = $student['enroll_no'];

        $check_in_db = FeedbackByStudent::where('enroll_no',$enroll_no)->where('placement_id',$placement_id)->first();

        if(sizeof($check_in_db)!=0)
        {

            return "true";

        }
        else
        {

            return "false";

        }


    }

    public function giveFeedback(CreateFeedback $request, $user_id, $placement_id)
    {

        $input = $request->only('description','rating');

        $check_in_db = Feedback::where('placement_id',$placement_id)->first();

        if(sizeof($check_in_db)!=0)
        {

            return $check_in_db;

        }

        $input['placement_id'] = $placement_id;

        $feedback = Feedback::create($input);

        if(!$feedback)
        {

            return Helper::apiError("Could not create feedback!",null,404);

        }
//
//        $user = User::where('id',$user_id)->first();
//
//        $company = Company::where('user_id',$user_id)->first();
//
//        $placement_primary = PlacementPrimary::where('placement_id',$placement_id)->first();
//
//        if(!$user && !$placement_primary && !$company)
//        {
//
//            return Helper::apiError("Details not found!",null,404);
//
//        }
//
//        $email = $user['email'];
//
//        $name = $company['company_name'];
//
//        $data = [
//
//            'email' => $email,
//            'description' => $input['description'],
//            'rating' => $input['rating'],
//            'placement' => $placement_primary,
//            'name' => $name,
//
//        ];
//
//        Mail::to('spc@daiict.ac.in')->send(new FeedbackEmail($data));

        return $feedback;

    }


    public function giveFeedbackByStudent(CreateFeedback $request, $user_id, $placement_id)
    {

        if (is_null($user_id)) {

            $student = request()->user()->student;

        } else {

            $student = User::find($user_id)->student;

        }

        $enroll_no = $student['enroll_no'];

        $check_in_db = FeedbackByStudent::where('enroll_no',$enroll_no)->where('placement_id',$placement_id)->first();

        if(sizeof($check_in_db)!=0)
        {

            return $check_in_db;

        }

        $input = $request->only('description','rating');

        $input['placement_id'] = $placement_id;

        $input['enroll_no'] = $enroll_no;

        $feedback = FeedbackByStudent::create($input);

        if(!$feedback)
        {

            return Helper::apiError("Could not create feedback!",null,404);

        }
//
//        $placement_primary = PlacementPrimary::where('placement_id',$placement_id)->first();
//
//        if(!$placement_primary)
//        {
//
//            return Helper::apiError("Details not found!",null,404);
//
//        }
//
//        $email = "$student['enroll_no']@daiict.ac.in";
//
//        $data = [
//
//            'email' => $email,
//            'description' => $input['description'],
//            'rating' => $input['rating'],
//            'placement' => $placement_primary,
//
//        ];
//
//        Mail::to('spc@daiict.ac.in')->send(new FeedbackEmail($data));

        return $feedback;

    }

    public function getFeedbackList($user_id, $placement_id)
    {

        $feedback_given = Feedback::where('placement_id',$placement_id)->first();

        $list = [];

        if(sizeof($feedback_given)!=0)
        {

            $placement = PlacementPrimary::where('placement_id',$placement_id)->first();

            $company = Company::where('id',$placement['company_id'])->first();

            $user = User::where('id',$company['user_id'])->first();

            $company['email'] = $user['email'];

            $company['contact_person'] = $user['name'];

            $company['description'] = $feedback_given['description'];

            $company['rating'] = $feedback_given['rating'];

            array_push($list, $company);

        }

        $feedback_given_by_user = FeedbackByStudent::where('placement_id',$placement_id)->get();

        if(sizeof($feedback_given_by_user)!=0)
        {

            foreach ($feedback_given_by_user as $single)
            {

                $student = Student::where('enroll_no',$single['enroll_no'])->first();

                $user = User::where('id',$student['user_id'])->first();

                $student['email'] = $user['email'];

                $student['name'] = $user['name'];

                $student['description'] = $single['description'];

                $student['rating'] = $single['rating'];

                array_push($list,$student);

            }

        }

        if(sizeof($list)==0)
        {

            return response("No Feedback",200);

        }

        return $list;


    }

}
