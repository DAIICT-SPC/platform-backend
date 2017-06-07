<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class ForgotPasswordRecoveryEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $code;
    public $url;

    public function __construct($data)
    {

        $this->code = $data['code'];
        $this->url = $data['url'];

    }

    public function build()
    {

        return $this->markdown('mail.forgot_password_recovery')
                    ->from('spc@daiict.ac.in')
                    ->subject('SPC - Recovery for Forgot Password')
                    ->with([
                        'code' => $this->code,
                        'url' => $this->url,
                    ]);

    }
}
