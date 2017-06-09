<?php

namespace App\Http\Controllers;

use App\Company;
use App\Feedback;
use App\Helper;
use App\Http\Requests\CreateFeedback;
use App\Mail\FeedbackEmail;
use App\PlacementPrimary;
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

}
