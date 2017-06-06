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
        $companies = Company::with(['user'])->get();

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

        $user = User::where('id',$user_id)->first();

        if(!$user)
        {

            return Helper::apiError("No Name found",null,404);

        }

        $company['name'] = $user['name'];

        $company['email'] = $user['email'];

        $company['profile_picture'] = $user['profile_picture'];

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

        $company_contact_name = $request->only('name');     //contact person name

        $company_contact_name = array_filter($company_contact_name, function($value){

            return $value != null;

        });

        $user = User::where('id',$user_id)->first();

        $user->update($company_contact_name);

        return $company;

    }

}
