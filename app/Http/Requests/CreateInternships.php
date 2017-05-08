<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateInternships extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'company_name' => 'required',
            'title' => 'required',
            'duration' => 'required',
            'job_profile' => 'required|max:50',
            'description' => 'required',
            'stipend' => 'required',
        ];
    }
}
