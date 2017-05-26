<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\Requests;

use App\Http\Requests\Request;

class CreateUser extends Request
{

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'password' => 'bail|required|string|min:6',
            ];
    }
}
