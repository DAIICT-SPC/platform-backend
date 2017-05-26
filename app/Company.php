<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{

    protected $table = "companys";

    protected $fillable = [ 'user_id', 'address', 'company_name', 'contact_no', 'company_expertise', 'company_url'];

    public function placementPrimary()
    {
        return $this->hasMany(PlacementPrimary::class);
    }

}
