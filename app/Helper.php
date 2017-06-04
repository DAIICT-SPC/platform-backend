<?php
/**
 * Created by PhpStorm.
 * User: Gaurav
 * Date: 5/3/2017
 * Time: 6:22 PM
 */

namespace App;

use App\Http\Requests\Request;

class Helper extends Request
{

    public static function apiError($message='',$content=null,$code=500){

        $error = ['error'=>true,'message'=>$message];

        if(!is_null($message)){
            $error['content'] = $content;
        }

        return response($error,$code);
    }

}