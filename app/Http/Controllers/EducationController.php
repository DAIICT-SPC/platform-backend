<?php

namespace App\Http\Controllers;

use App\Helper;
use App\Http\Requests\CreateEducation;
use Illuminate\Http\Request;
use App\Education;

class EducationController extends Controller
{

    public function index()
    {

        $education_list = Education::all();

        return $education_list;

    }

    public function createNew(CreateEducation $request)
    {

        $education_input = $request->only('name');

        if(!$education_input)
        {
            Helper::apiError('Please Enter a valid Education Detail',null,404);
        }

        $education = Education::create($education_input);

        return $education;

    }

    public function updateEducation(Request $request, $id)
    {

        $education = Education::find($id);

        if(!$education){
            return Helper::apiError('Education not Found!',null,404);
        }

        $input = $request->only('name');

        $input = array_filter($input, function($value){
            return $value != null;
        });

        $education->update($input);

        return $education;

    }

    public function destroy($id)
    {

        $education = Education::find($id);

        if(!$education){

            return Helper::apiError('Education not Found!',null,404);

        }

        $education->delete();

        return response("",204);

    }

}
