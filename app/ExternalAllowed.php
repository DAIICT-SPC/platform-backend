<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ExternalAllowed extends Model
{
    protected $table = 'external_allowed';

    protected $fillable = [
      'placement_id', 'enroll_no', 'user_id',
    ];

    public function placements()
    {
        return $this->belongsToMany(PlacementPrimary::class,'external_allowed','placement_id','placement_id');
    }

    public function users()
    {
        return $this->belongsToMany(User::class,'external_allowed','user_id','id');
    }

    public function students()
    {
        return $this->belongsToMany(User::class,'external_allowed','enroll_no','enroll_no');
    }

}
