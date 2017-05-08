<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateActivation;
use Illuminate\Http\Request;
use App\Activation;
use App\Helper;
use Illuminate\Support\Facades\File;
use Mail;

class ActivationController extends Controller
{

    public function index()
    {

    }


    public function showAllActivation()
    {
        $activations = Activation::all();

        if(!$activations){
            Helper::apiError('No Activations found',null,404);
        }

        return $activations;
    }


    public function createSingleEntry(CreateActivation $request)
    {

        $input = $request->only('email','role');      //creates array

        $input['code'] = str_random(15);            //need to check if the string does'nt repeat in database

        $activation = Activation::create($input);

        if(!$activation){

            return Helper::apiError("Activation cannot be Created!");

        }

//        $this->activationEmail($activation);

        return $activation;

    }


    /*    public function createViaFile(Request $request)
    {

        $inputfile = $request->file('csv');      //getting file - name of tag is csv

        if($inputfile==null) {
            return Helper::apiError('File not uploaded', null, 404);
        }

        try
        {
            $filecontents = File::get($inputfile->getRealPath());
        }
        catch (Illuminate\Filesystem\FileNotFoundException $exception)
        {
            Helper::apiError('File not found',null,404);
        }

        foreach ($filecontents as $filecontent){

            $input['email'] = $filecontent;

            $input['role'] = $request->only('role');

            $input['code'] = str_random(15);

            $activation = Activation::create($input);

            if(!$activation){

                return Helper::apiError("Activation cannot be Created!");

            }

        }

    }   */


    public function findCode($code)                         //If someone goes at link   abc.com/activation/activate/{code}
    {
        $activation = Activation::where('code',$code)->first();

        if(!$activation){
            Helper::apiError('No such activation code exist. Please try again.',null,404);
        }

        return $activation;                                 //this will return email, role(hidden), code(hidden)
    }

    public function activationEmail($input){

        $data = [

            'code' => $input['code'],                   //You can access it directly as $code and $link in EMAIL BLADE - {{$code}}

            ];

        Mail::send('EMAIL BLADE', $data, function($message) use($input) {

            $message->to($input['email'])->subject('SPC activation code');

            $message->from('spc@daiict.ac.in','SPC');

        });

    }

}
