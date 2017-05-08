<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'email',  'role', 'password',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];


    public function company()
    {
        return $this->hasOne(Company::class, 'user_id');
    }

}
