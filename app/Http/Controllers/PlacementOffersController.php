<?php

namespace App\Http\Controllers;

use App\Helper;
use App\Http\Requests\CreateOffer;
use App\Job_Type;
use App\Mail\SelectedAndOfferMail;
use App\Offer;
use App\PlacementPrimary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class PlacementOffersController extends Controller
{

    public function giveOfferLetter(CreateOffer $request, $placement_id)        //insert in OFFER Block.. check if student completed last round PlacementController@checkIfRoundsCompleted
    {

        $enroll_nos = $request->only('enroll_no');

        $enroll_no = $enroll_nos['enroll_no'];

        $input = $request->only('package');

        $input['placement_id'] = $placement_id;

        $placement_primary = PlacementPrimary::with(['company'])->where('placement_id', $placement_id)->first();

        if (!$placement_primary) {

            return Helper::apiError("No Placement Found with this id!", null, 404);

        }


        $job_title = $placement_primary['job_title'];

        $location = $placement_primary['location'];

        $company_name = $placement_primary["company"]['company_name'];

        $job_type = Job_Type::where('id', $placement_primary['job_type_id'])->pluck('job_type');

        $job_type_name = $job_type[0];

        $data = [

            'job_title' => $job_title,
            'location' => $location,
            'company_name' => $company_name,
            'job_type_name' => $job_type_name,
            'offer' => $input['package'],

        ];


        $offer_db = Offer::where('placement_id', $input['placement_id'])->where('enroll_no', $enroll_no)->first();

        if (is_null($offer_db)) {

            $input['enroll_no'] = $enroll_no;

            $offer = Offer::create($input);

            Mail::to("$enroll_no@daiict.ac.in")->send(new SelectedAndOfferMail($data));

        } else {

            $offer_db->update($input);

        }

        return $offer;

    }

    public function cancelOfferLetter(Request $request, $placement_id)
    {

        $input = $request->only('enroll_no');

        $input['placement_id'] = $placement_id;

        $offer = Offer::where('placement_id',$input['placement_id'])->where('enroll_no',$input['enroll_no'])->first();

        if( is_null($offer) )
        {

            return Helper::apiError("No offer letter for such enroll no.",null,404);

        }

        $offer->delete();

        return response("",204);

    }

    public function getAllOfferLetter()         //for admin
    {

        $offers = Offer::all();

        if( !$offers )
        {

            Helper::apiError("Error in finding offers!",null,404);

        }

        $all_offers = [];

        $i = 0;

        foreach ( $offers as $offer )
        {

            $placement_id = $offer['placement_id'];

            $placement_primary = PlacementPrimary::where('placement_id',$placement_id)->first();

            $offer['placement_primary'] = $placement_primary;

            $all_offers[$i] = $offer;

            $i++;

        }

        return $all_offers;

    }

    public function getOfferLetter($enroll_no_or_placement_id)
    {

        $offer = Offer::where('enroll_no',$enroll_no_or_placement_id)->orWhere('placement_id',$enroll_no_or_placement_id)->first();

        if( !$offer )
        {

            Helper::apiError("Error in finding offer!",null,404);

        }

        return $offer;

    }

}
