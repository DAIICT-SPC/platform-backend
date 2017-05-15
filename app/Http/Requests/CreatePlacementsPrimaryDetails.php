<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreatePlacementsPrimaryDetails extends FormRequest
{

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'job_title' => 'bail|required|max:191',
            'job_description' => 'bail|required',
            'last_date_for_registration' => 'required',
            'location' => 'bail|required|max:191',

            'package' => 'bail|required',
        ];
    }

}
