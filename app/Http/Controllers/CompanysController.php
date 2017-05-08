<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Company;
use App\Helper;
use App\User;

class CompanysController extends Controller
{

    public function index()
    {
        //
    }

    public function show($user_id = null)
    {
        if (is_null($user_id)) {

            $company = request()->user()->company;

        } else {
            $company = User::find($user_id)->company;            //first() because only one entry would be there in student for one user
        }

        if(!$company){
            return Helper::apiError('No such Entry found for Company!',null,404);
        }

        return $company;

    }

    public function update(Request $request, $user_id)
    {

        $user = Company::where('user_id',$user_id)->first();

        if(!$user){
            Helper::apiError('No such Entry found for Company',null,'404');
        }

        $input = $request->only('company_name', 'address', 'contact_person', 'contact_no', 'company_expertise', 'company_url');

        $input = array_filter($input, function($value){
            return $value != null;
        });

        $user->update($input);

        return $user;

    }

}
