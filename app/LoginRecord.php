<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LoginRecord extends Model
{

    protected $table = 'login_records';

    protected $fillable = [
      'from_id' , 'to_id' , 'reason'
    ];

    public function fromUsers()
    {
        return $this->belongsTo(User::class,'from_id');
    }

    public function toUsers()
    {
        return $this->belongsTo(User::class,'to_id');
    }

}
