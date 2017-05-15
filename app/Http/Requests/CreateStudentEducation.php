<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateStudentEducation extends FormRequest
{

    public function authorize()
    {
        return true;
    }


    public function rules()
    {
        return [
            'clg_school' => 'required',
            'start_year' => 'required',
            'end_year' => 'required',
            'drive_link' => 'required',
            'education_id' => 'required',
            'cpi' => 'required',
        ];
    }
}
