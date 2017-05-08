<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Http\Requests\Request;

class CreateActivation extends Request
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
       return [


            ];

  }
}
