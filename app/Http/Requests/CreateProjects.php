<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateProjects extends FormRequest
{

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'project_name' => 'required',
            'duration' => 'required',
            'contribution' => 'required',
            'description' => 'required',
            'under_professor' => 'required',
        ];
    }

}
