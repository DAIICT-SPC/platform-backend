<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

use App\Helper;

use Illuminate\Http\JsonResponse;


class Request extends FormRequest
{

    public function response(array $errors)
    {
        return Helper::apiError('Fault in Form Validation', $errors,422);
    }

}
