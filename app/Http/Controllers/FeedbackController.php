<?php

namespace App\Http\Controllers;

use App\Feedback;
use App\Helper;
use App\Http\Requests\CreateFeedback;
use Illuminate\Http\Request;

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

        return $feedback;

    }

}
