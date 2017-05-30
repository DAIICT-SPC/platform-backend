<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{

    protected $table = "companys";

    protected $fillable = [ 'user_id', 'address', 'company_name', 'contact_no', 'company_expertise', 'company_url'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function placements()
    {
        return $this->hasMany(PlacementPrimary::class,'company_id');
    }

    public function placement_seasons()
    {
        return $this->belongsToMany(PlacementSeason::class,'placements_season_company','company_id','placement_season_id');
    }

}
