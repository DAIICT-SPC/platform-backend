<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreatePreviousEducation extends Request
{

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'clg_school' => 'required',
            'education' => 'required',
            'grade_percent' => 'required',
            'start_year' => 'required',
            'end_year' => 'required',
            'drive_link' => 'required'
        ];
    }

}
