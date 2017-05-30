<?php

namespace App\Http\Controllers;

use App\Helper;
use App\Http\Requests\CreatePlacementSeason;
use App\PlacementSeason;
use Illuminate\Http\Request;

class PlacementSeasonController extends Controller
{

    public function create(CreatePlacementSeason $request)
    {
        $input = $request->only('title');

        $placement_season = PlacementSeason::create($input);

        if(!$placement_season)
        {
            return Helper::apiError("Cant Create Placement Season!",null,404);
        }

        return $placement_season;

    }

    public function show($placement_season_id)
    {

        $placement_season = PlacementSeason::where('id',$placement_season_id)->first();

        if(!$placement_season)
        {
            return Helper::apiError("No Placement Season!",null,404);
        }

        return $placement_season;

    }

    public function update(Request $request, $placement_season_id)
    {

        $input = $request->only('title');

        $input = array_filter($input, function($value){

            return $value != null;

        });

        $placement_season = PlacementSeason::where('id',$placement_season_id)->first();

        if(!$placement_season)
        {
            return Helper::apiError("No Placement Season!",null,404);
        }

        $placement_season->update($input);

        return $placement_season;

    }

    public function destroy($placement_season_id)
    {

        $placement_season = PlacementSeason::where('id',$placement_season_id)->first();

        if(!$placement_season)
        {
            return Helper::apiError("No Placement Season!",null,404);
        }

        $placement_season->delete();

        return response("",200);

    }

    public function startSeason()
    {

    }

    public function closeSeason()
    {

    }

    public function allowCompany()
    {

    }

    public function disallowCompany()
    {

    }
}
