<?php

namespace App\Http\Controllers;

use App\Helper;
use App\LoginRecord;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\JWTAuth;

class AuthController extends Controller
{

    public function authenticate(Request $request)
    {

        $credentials = $request->only('email','password');

        try{

            if( ! $token = \JWTAuth::attempt($credentials) ){
                return Helper::apiError("User Credentials are not Correct!",null,404);
            }

        }catch (JWTException $err) {


            Helper::apiError("Something went wrong!",null,500);

        }

        $checkRole = User::where('email',$credentials['email'])->first();

        if($checkRole && $checkRole['role'] == 'admin')
        {

            $input = $request->only('reason');

            $input['from_id'] = $checkRole['id'];

            $input['to_id'] = $checkRole['id'];

            $login_record = LoginRecord::create($input);

            if(sizeof($login_record)==0)
            {
                return Helper::apiError("Could not store login record!",null,404);
            }

        }

        return response()->json(compact('token'));

    }

    public function checkIfAdmin(Request $request)
    {

        $credentials = $request->only('email','password');

        $checkRole = User::where('email',$credentials['email'])->first();

        if(!$checkRole)
        {
            return Helper::apiError("Could not find user!",null,404);
        }

        if($checkRole['role'] == 'admin')
        {

            return response(array('status' => true), 200);

        }

        return response(array('status' => false), 200);

    }

    public function checkAuthentication()
    {

        $token = \JWTAuth::getToken();

        $user = \JWTAuth::toUser($token);

        return $user;

    }

    public function loginAs(Request $request, $user_id, $to_user_id)
    {

        $user = User::where('id',$to_user_id)->first();

        if (!$token=\JWTAuth::fromUser($user)) {

            return response()->json(['error' => 'invalid_credentials'], 401);

        }

        $input = $request->only('reason');

        $input['from_id'] = $user_id;

        $input['to_id'] = $to_user_id;

        $login_record = LoginRecord::create($input);

        if(!$login_record)
        {

            return Helper::apiError("Cannot create Credentials!",null,404);

        }

        return response()->json(compact('token'));

    }

}
