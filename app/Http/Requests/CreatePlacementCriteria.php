<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreatePlacementCriteria extends FormRequest
{

    public function authorize()
    {
        return true;
    }

   public function rules()
    {
        return [
            'education_id' => 'required',
            'cpi_required' => 'required',
        ];
    }
}
