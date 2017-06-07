<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ForgotPassword extends Model
{

    protected $table = 'forgot_passwords';

    protected $fillable = [
      'email', 'code'
    ];

}
