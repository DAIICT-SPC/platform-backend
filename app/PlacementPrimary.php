<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Company;
use App\PlacementOpenFor;

class PlacementPrimary extends Model
{
    protected $table = 'placements_primary';

    protected $fillable = [
        'job_title',
        'job_description',
        'last_date_for_registration',
        'location',
        'no_of_students',
        'package',
        'job_type_id',
        'company_id',
        'status',
    ];

    public function company(){
        return $this->belongsTo(Company::class,'company_id');
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'placements_open_for', 'placement_id', 'category_id');
    }
}
