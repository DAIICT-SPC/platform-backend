<?php

namespace App\Http\Controllers;

use App\Helper;
use App\Http\Requests\CreateOffer;
use App\Offer;
use App\PlacementPrimary;
use Illuminate\Http\Request;

class PlacementOffersController extends Controller
{

    public function giveOfferLetter(CreateOffer $request, $placement_id)
    {

        $enroll_check_boxes = $request->only('enroll_no');

        $input = $request->only('package');

        $input['placement_id'] = $placement_id;

        $enroll_nos  = array_values($enroll_check_boxes);

        $offer = [];

        $i=0;

        foreach ($enroll_nos as $enroll_no)
        {

            foreach ( $enroll_no as $single) {

                $offer_db = Offer::where('placement_id', $input['placement_id'])->where('enroll_no', $enroll_no)->first();

                if (is_null($offer_db)) {

                    $input['enroll_no'] = $single;

                    $offer[$i] = Offer::create($input);

                    $i++;

                }else{

                    $offer[$i] = $offer_db;

                    $i++;

                }

            }

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
