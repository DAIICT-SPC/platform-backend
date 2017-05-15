<?php

namespace App\Http\Controllers;

use App\Helper;
use Illuminate\Http\Request;
use App\Job_Type;
use App\Http\Requests\CreateJobType;

class JobTypeController extends Controller
{

    public function index(){

        $job_types = Job_Type::all();

        if(!$job_types){
            Helper::apiError('No Job Types found',null,404);
        }

        return $job_types;
    }

    public function create(CreateJobType $request)
    {

        $input = $request->only('job_type','duration');

        $job_type = Job_Type::create($input);

        if(!$job_type){
            return Helper::apiError('Job Type cannot created');
        }

        return $job_type;

    }

    public function show($id)               //finds job type with particular id
    {

        $job_type = Job_Type::find($id);

        if(!$job_type){
            Helper::apiError('No Job Type found with such id!',null,404);
        }

        return $job_type;

    }

    public function update(Request $request, $id)
    {

        $job_type = Job_Type::find($id);

        if(!$job_type){
            Helper::apiError('No Job Type found with such id',null,404);
        }

        $input = $request->only('job_type','duration');

        $input = array_filter($input, function($value){
            return $value != null;
        });

        $job_type->update($input);

        return $job_type;

    }

    public function destroy($id)
    {

        $job_type = Job_Type::find($id);

        if(!$job_type){
            Helper::apiError('No Job Type found with such id',null,404);
        }

        $job_type->delete();

        return response("",204);

    }

}
