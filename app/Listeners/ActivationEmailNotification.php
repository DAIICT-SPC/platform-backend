<?php

namespace App\Listeners;

use App\Events\EmailNotification;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class ActivationEmailNotification
{

    public function __construct()
    {
        //
    }

    public function handle(EmailNotification $event)
    {



    }
}
