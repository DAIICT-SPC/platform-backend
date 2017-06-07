<?php

namespace App\Http\Controllers;

use App\Company;
use App\Helper;
use App\Http\Requests\Allow_Disallow_Company;
use App\Http\Requests\CreatePlacementSeason;
use App\PlacementPrimary;
use App\PlacementSeason;
use App\PlacementSeason_Company;
use Illuminate\Http\Request;
use Psy\Output\ShellOutput;

class PlacementSeasonController extends Controller
{

    public function index()
    {
        $placement_season_list = PlacementSeason::all();

        if(!$placement_season_list)
        {
            return Helper::apiError("No Placement Season!",null,404);
        }

        return $placement_season_list;
    }

    public function create(CreatePlacementSeason $request)
    {
        $input = $request->only('title');

        $placement_season_db = PlacementSeason::where('title',$input['title'])->first();

        if($placement_season_db)
        {

            return $placement_season_db;

        }

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

        $company_id = $input['company_id'];

        $placements = PlacementSeason::find($placement_season_id);

        if(!$placements)
        {
            return Helper::apiError("No Placement Found!",null,404);
        }

        $placements->companies()->detach($company_id);

        return response("",200);

    }

    public function allAllowedCompanies($placement_season_id)
    {

        $allowed_companies = PlacementSeason_Company::where('placement_season_id',$placement_season_id)->pluck('company_id');

        if(!$allowed_companies or is_null($allowed_companies))
        {
            return Helper::apiError("No Companies allowed Yet!",null,404);
        }

        $companies_list = Company::whereIn('id',$allowed_companies)->orderBy('company_name','asc')->get();

        return $companies_list;

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

        $remain_companies = Company::whereIn('id', $remaining_companies)->orderBy('company_name','asc')->get();;

        if(!$remain_companies)
        {

            return Helper::apiError("No Company Detail Found!",null,404);

        }

        return $remain_companies;

    }

    public function showPlacementSeasonAvailable($user_id)
    {

        $company = Company::where('user_id',$user_id)->first();

        $placement_seasons = PlacementSeason::with(['companies' => function($q) use($company){
            $q->where('companys.id',$company['id']);
        }])->where('status','=','open')->get();

        if(!$placement_seasons or sizeof($placement_seasons) == 0)
        {
            return response('No Placement Season Found!',200);
        }

        $placement_season_list = [];

        foreach ( $placement_seasons as $placement_season)
        {

            if(sizeof($placement_season['companies']) != 0)
            {

                array_push($placement_season_list,$placement_season);

            }

        }

        return $placement_season_list;

    }

    public function showPlacementSeasonAvailableToCompany($user_id, $company_id)
    {

        $placement_seasons = PlacementSeason::with(['companies' => function($q) use($company_id){
            $q->where('companys.id',$company_id);
        }])->where('status','=','open')->get();

        if(!$placement_seasons or sizeof($placement_seasons) == 0)
        {
            return Helper::apiError("No Placement Season Found!",null,404);
        }

        $placement_season_list = [];

        foreach ( $placement_seasons as $placement_season)
        {

            if(sizeof($placement_season['companies']) != 0)
            {

                array_push($placement_season_list,$placement_season);

            }

        }

        return $placement_season_list;

    }

    public function placementsInPlacementSeason($placement_season_id)
    {

        $all_placements = PlacementPrimary::with(['company','placement_season' => function($q) use($placement_season_id){
            $q->where('id',$placement_season_id);
        }])->where('status','!=','draft')->get();

        $placement_drive_list = [];

        foreach ($all_placements as $placement)
        {

            if(is_null($placement['placement_season']))
            {

            }else{
               array_push($placement_drive_list,$placement);
            }

        }

        return $placement_drive_list;

    }

    public function placementsCompanyWiseListing($placement_season_id, $company_id)
    {

        $all_placements = PlacementPrimary::with(['company','placement_season' => function($q) use($placement_season_id){
            $q->where('id',$placement_season_id);
        }])->where('status','!=','draft')->where('company_id',$company_id)->get();

        $placement_drive_list = [];

        foreach ($all_placements as $placement)
        {

            if(is_null($placement['placement_season']))
            {

            }else{
                array_push($placement_drive_list,$placement);
            }

        }

        return $placement_drive_list;

    }

    public function companiesAllowedOrNot($placement_season_id)
    {

        $allowed_companies_detail = PlacementSeason_Company::where('placement_season_id',$placement_season_id)->get();

        $allowed_companies = $allowed_companies_detail->pluck('company_id');

        $companies = Company::all();

        if(!$allowed_companies or is_null($allowed_companies))
        {

            return $companies;

        }

        $company_ids = $companies->pluck('id');

        $remaining_companies = array_diff($company_ids->toArray(),$allowed_companies->toArray());

        $allowed_companies = Company::whereIn('id', $allowed_companies)->get();;

        $remain_companies = Company::whereIn('id', $remaining_companies)->get();;

        if(!$remain_companies)
        {

            return Helper::apiError("No Company Detail Found!",null,404);

        }

        $companies_allowed_or_not = [];

        foreach ($allowed_companies as $allowed_company)
        {

            $temp['company_detail'] = $allowed_company;

            $temp['status'] = 'allowed';

            array_push($companies_allowed_or_not,$temp);

        }

        foreach ($remain_companies as $remain_company)
        {

            $temp['company_detail'] = $remain_company;

            $temp['status'] = 'Not Allowed';

            array_push($companies_allowed_or_not,$temp);

        }

        shuffle($companies_allowed_or_not);

        return $companies_allowed_or_not;

    }

}
