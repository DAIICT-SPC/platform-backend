<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Activation extends Model
{
    protected $table = "activation";

    protected $fillable = ['email','role','code'];
}
