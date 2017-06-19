<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class PlacementPrimary extends Model
{
    protected $primaryKey = 'placement_id';

    protected $table = 'placements_primary';

    protected $fillable = [
        'job_title',
        'job_description',
        'last_date_for_registration',
        'location',
        'no_of_students',
        'package',
        'job_type_id',
        'start_date',
        'end_date',
        'company_id',
        'status',
        'placement_season_id',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class,'company_id');
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'placements_open_for', 'placement_id', 'category_id');
    }

    public function externally_allowed()
    {
        return $this->belongsToMany(Student::class, 'external_allowed', 'placement_id', 'enroll_no');
    }

    public function criterias()
    {
        return $this->hasMany(PlacementCriteria::class, 'placement_id');
    }

    public function applications()
    {
        return $this->hasMany(Application::class,'placement_id');
    }

    public function placementSelection()
    {
        return $this->hasMany(SelectionRound::class,'placement_id');
    }

    public function jobType()
    {
        return $this->belongsTo(Job_Type::class,'job_type_id');
    }

    public function studentsInRound($round_no)
    {
        return $this->belongsToMany(Student::class, 'select_students_roundwise', 'placement_id', 'enroll_no')
            ->where('select_students_roundwise.round_no', "<=", $round_no);
    }

    public function offers()
    {
        return $this->hasMany(Offer::class,'placement_id');
    }

    public function placement_season()
    {
        return $this->belongsTo(PlacementSeason::class,'placement_season_id');
    }

    public function studentDataAllowance()
    {
        return $this->hasOne(StudentDataAllowance::class,'placement_id');
    }

}
