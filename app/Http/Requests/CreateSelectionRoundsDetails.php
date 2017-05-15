<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateSelectionRoundsDetails extends FormRequest
{

     public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'round_no' => 'bail|required|numeric',
            'round_name' => 'bail|required|max:191',
            'round_description' => 'bail|required|min:10',
        ];
    }

}
