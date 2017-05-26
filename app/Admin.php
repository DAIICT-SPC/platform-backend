<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Admin extends Model
{

    protected $table = "admin";

    protected $fillable = [ 'user_id', 'contact_no', 'position'];


    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

}
