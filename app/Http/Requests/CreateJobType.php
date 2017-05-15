<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateJobType extends FormRequest
{

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'job_type' => 'bail|required|max:191',
            'duration' => 'bail|required|max:191',
        ];
    }
}
