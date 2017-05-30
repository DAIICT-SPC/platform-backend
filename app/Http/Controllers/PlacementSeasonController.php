<?php

namespace App\Http\Controllers;

use App\Company;
use App\Helper;
use App\Http\Requests\Allow_Disallow_Company;
use App\Http\Requests\CreatePlacementSeason;
use App\PlacementSeason;
use App\PlacementSeason_Company;
use Illuminate\Http\Request;
use Psy\Output\ShellOutput;

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

    public function startSeason($placement_season_id)
    {

        $placement_season = PlacementSeason::where('id',$placement_season_id)->first();

        if(!$placement_season)
        {
            return Helper::apiError("No Placement Season!",null,404);
        }

        $placement_season->update(array('status'=>'open'));

        return $placement_season;

    }

    public function closeSeason($placement_season_id)
    {
        $placement_season = PlacementSeason::where('id',$placement_season_id)->first();

        if(!$placement_season)
        {
            return Helper::apiError("No Placement Season!",null,404);
        }

        $placement_season->update(array('status'=>'closed'));

        return $placement_season;

    }

    public function allowCompany(Allow_Disallow_Company $request, $placement_season_id)
    {
        $input = $request->only('company_id');

        $company = Company::where('id',$input['company_id'])->first();

        if(!$company or is_null($company))
        {
            return Helper::apiError("No Company Found!",null,404);
        }

        $check_in_db = PlacementSeason_Company::where('placement_season_id',$placement_season_id)->where('company_id',$input['company_id'])->first();

        if(!is_null($check_in_db) or !$check_in_db)
        {

            $input['placement_season_id'] = $placement_season_id;

            $allowedCompany = PlacementSeason_Company::create($input);

            if(!$allowedCompany)
            {
                return Helper::apiError("Cant Insert!",null,404);
            }

            return $allowedCompany;

        }

        return $check_in_db;

    }

    public function allowCompanies(Request $request, $placement_season_id)
    {

        $input = $request->only('company_id');

        $company_list = $input['company_id'];

        $placements = PlacementSeason::find($placement_season_id);

        if(!$placements)
        {
            return Helper::apiError("No Placement Found!",null,404);
        }

        $placements->companies()->sync($company_list);

        return response("",200);

    }

    public function disallowCompany(Request $request, $placement_season_id)
    {

        $input = $request->only('company_id');

        $company_list = $input['company_id'];

        $placements = PlacementSeason::find($placement_season_id);

        if(!$placements)
        {
            return Helper::apiError("No Placement Found!",null,404);
        }

        $placements->companies()->detach($company_list);

        return response("",200);

    }

    public function allAllowedCompanies($placement_season_id)
    {

        $allowed_companies = PlacementSeason::with(['companies'])->where('id',$placement_season_id)->get();

        if(!$allowed_companies or is_null($allowed_companies))
        {
            return Helper::apiError("No Companies allowed Yet!",null,404);
        }

        return $allowed_companies;

    }

    public function remainingCompanies($placement_season_id)
    {

        $allowed_companies = PlacementSeason_Company::where('placement_season_id',$placement_season_id)->pluck('company_id');

        $companies = Company::all();

        if(!$allowed_companies or is_null($allowed_companies))
        {

            return $companies;

        }

        $company_ids = $companies->pluck('id');

        $remaining_companies = array_diff($company_ids->toArray(),$allowed_companies->toArray());

        $remain_companies = [];

        foreach ($remaining_companies as $id)
        {

            $company = Company::find($id);

            array_push($remain_companies,$company);

        }

        return $remain_companies;

    }

}
