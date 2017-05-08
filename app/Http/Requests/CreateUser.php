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
            'email' => 'bail|required|string|email|max:255',
            'password' => 'bail|required|string|min:6',
            'role' => 'bail|required',
        ];
    }
}
