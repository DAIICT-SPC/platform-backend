<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $table = "companys";

    protected $fillable = ['company_name', 'user_id', 'address', 'contact_person', 'contact_no', 'company_expertise', 'company_url'];

}
