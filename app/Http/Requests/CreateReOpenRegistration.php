<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateReOpenRegistration extends FormRequest
{


    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'last_date_for_registration' => 'required'
        ];
    }
}
