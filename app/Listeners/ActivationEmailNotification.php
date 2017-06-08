<?php

namespace App\Listeners;

use App\Events\ActivationCreated;
use App\Events\EmailNotification;
use App\Mail\ActivationEmail;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

class ActivationEmailNotification
{

    public function __construct()
    {

    }

    public function handle(ActivationCreated $input)
    {

        foreach ($input as $a)
        {

            $email = $a['email'];

            $code =  $a['code'];

            $data = [

                'code' => $code,

                'url' => "http://localhost:8080/signup/$code"

            ];

            $name = null;

            Mail::to($email, $name)->send(new ActivationEmail($data));

        }

    }

}
