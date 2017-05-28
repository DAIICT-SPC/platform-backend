<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateActivation;
use Illuminate\Http\Request;
use App\Activation;
use App\Helper;
use Illuminate\Support\Facades\File;
use Mail;
use Maatwebsite\Excel\Facades\Excel;
use App\User;

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

        $input['code'] = time().str_random(5);            //need to check if the string does'nt repeat in database

        $activation = Activation::create($input);

        if(!$activation){

            return Helper::apiError("Activation cannot be Created!");

        }

    //        $this->activationEmail($activation);

        return $activation;

    }


    public function createViaFile(Request $request)
    {

        $inputfile = $request->file('csv');      //getting file - name of tag is csv

        if($inputfile==null) {
            return Helper::apiError('File not uploaded', null, 404);
        }

        $emails = [];

        $activation_emails = Activation::pluck('email')->toArray();         //all the present emails in activation table

        $user_emails = User::pluck('email')->toArray();                            ////all the present emails in users table

        if($inputfile->getClientOriginalExtension() == 'txt')           //remember - txt file should have content ' , ' (comma) seperated
        {

            try {

                $filecontents = File::get($inputfile->getRealPath());

                $emails = explode(",", $filecontents);

                $email_diff_users = array_diff($emails, $user_emails);               //first checking for a entry already in users table

                $email_diff = array_diff($email_diff_users, $activation_emails);     //secondly checking for a entry in activation table

                $emails = array_values($email_diff);

                $emails = array_filter($emails, function($value){

                    return $value != null;

                });

            } catch (Illuminate\Filesystem\FileNotFoundException $exception) {

                Helper::apiError('File not found', null, 404);

            }

        }

        else if($inputfile->getClientOriginalExtension() == 'xlsx' or $inputfile->getClientOriginalExtension() == 'xls')
        {

            try {

                $data = Excel::load($inputfile, function($reader) {})->get();

                $datas = $data->toArray();

                $email = [];

                $i=0;

                    foreach ( $datas as $data) {                        // sheet1 then sheet2 then sheet 3

                        if (!empty($data) && !is_null($data)) {         // if either sheet is not empty

                            foreach ($data as $emails) {                // in single sheet fetching emails

                                $email_fetch = array_values($emails);

                                foreach ($email_fetch as $em)           //as each email is in a single array too thus converting in proper format

                                {

                                    $email[$i] = $em;

                                    $i++;

                                }

                            }

                        }

                    }

                $email_diff_users = array_diff($email, $user_emails);               //first checking for a entry already in users table

                $emails = array_diff($email_diff_users, $activation_emails);

                $emails = array_filter($emails, function($value){

                    return $value != null;

                });

            }

            catch (Illuminate\Filesystem\FileNotFoundException $exception) {

                Helper::apiError('File not found', null, 404);

            }

        }

            foreach ($emails as $email){

                $activation = [];

                $i = 0;

                $input = $request->only('role');

                $input['email'] = $email;

                $input['code'] = time().str_random(5);

                $activation[$i] = Activation::create($input);

                $i++;

                if(!$activation){

                    return Helper::apiError("Activation cannot be Created!");

                }

            }

        return $activation;

        //send mail to each activation email

    }


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
