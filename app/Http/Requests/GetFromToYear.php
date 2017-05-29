<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GetFromToYear extends FormRequest
{

    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'from_year' => 'required',
            'to_year' => 'required',
        ];
    }
}
