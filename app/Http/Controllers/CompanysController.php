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

        $input = $request->only('company_name', 'address', 'contact_person', 'contact_no', 'company_expertise', 'company_url');

        $input = array_filter($input, function($value){

            return $value != null;

        });

        $company->update($input);

        return $company;

    }

}
