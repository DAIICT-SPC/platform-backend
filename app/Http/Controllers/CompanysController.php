<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreatePlacementsPrimaryDetails;
use Illuminate\Http\Request;
use App\Company;
use App\Helper;
use App\User;

class CompanysController extends Controller
{

    public function index()             //for the admin to see all the list of companies
    {
        $companies = Company::all();

        if(!$companies)
        {
            return Helper::apiError("No Company Found!",null,404);
        }

        return $companies;
    }

    public function show($user_id = null)
    {

        if (is_null($user_id)) {

            $company = request()->user()->company;

        } else {

            $company = User::find($user_id)->company;

        }

        if(!$company){

            return Helper::apiError('No such Entry found for Company!',null,404);

        }

        $user_name = User::where('id',$user_id)->pluck('name');

        if(!$user_name)
        {

            return Helper::apiError("No Name found",null,404);

        }

        $company['name'] = $user_name[0];

        return $company;

    }

    public function update(Request $request, $user_id = null)
    {


        if (is_null($user_id)) {

            $company = request()->user()->company;

        } else {

            $company = User::find($user_id)->company;

        }

        if(!$company){

            return Helper::apiError('No such Entry found for Company!',null,404);

        }

        $input = $request->only('company_name', 'address', 'contact_no', 'company_expertise', 'company_url');

        $input = array_filter($input, function($value){

            return $value != null;

        });

        $company->update($input);

        return $company;

    }

}
