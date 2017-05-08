<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Admin extends Model
{

    protected $table = "admin";

    protected $fillable = ['name', 'user_id', 'contact_no', 'position'];

}
