<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SelectionRound extends Model
{
    protected $table = 'selection_rounds';

    protected $fillable = [
        'placement_id',
        'round_no',
        'round_name',
        'round_description',
        'date_of_round',
    ];

}
