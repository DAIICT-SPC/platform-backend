<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateCategory;
use App\Student;
use App\StudentEducation;
use Illuminate\Http\Request;
use App\Category;

use App\Helper;

class CategoryController extends Controller
{

    public function index()
    {
        $categories = Category::all();

        if(!$categories){
            Helper::apiError('Null Entries',null,404);
        }

        return $categories;
    }

    public function create(CreateCategory $request)         //Creates new category
    {

        $input = $request->only('name');

        $check_if_category_exist = Category::where('name',$input['name'])->first();

        if( is_null($check_if_category_exist) )
        {

            $category = Category::create($input);

            if(!$category){
                return Helper::apiError('Category not created');
            }

            return $category;

        }else{

            return $check_if_category_exist;

        }

    }

    public function show($id)               //finds category with particular id
    {
        $category = Category::find($id);

        if(!$category){
            Helper::apiError('No Category found with such id!',null,404);
        }

        return $category;
    }

    public function update(Request $request, $id)
    {

        $category = Category::find($id);

        if(!$category){
            Helper::apiError('No Category found with such id',null,404);
        }

        $input = $request->only('name');

        $input = array_filter($input, function($value){
            return $value != null;
        });

        $category->update($input);

        return $category;

    }

    public function destroy($id)
    {

        $category = Category::find($id);

        if(!$category){
            Helper::apiError('No Category found with such id',null,404);
        }

        $category->delete();

        return response("",204);
    }

    public function fetchEducationAccordingToCategory($category_id)
    {

        if($category_id==2)
        {
            return array(2,3,4);
        }else
        {
            return array(1,2,3,4);
        }

    }

    public function fetchEducationAccordingToCategoryForStudent($user_id)
    {

        $student = Student::where('user_id',$user_id)->first();

        if(!$student)
        {
            return Helper::apiError("Can't fetch Student!",null,404);
        }

        $category_id = $student['category_id'];

        if($category_id==2)
        {

            $education_db = StudentEducation::where('enroll_no',$student['enroll_no'])->pluck('education_id');

            if(sizeof($education_db)==0)
            {

                $final = array(2,3,4);

                return $final;

            }else{

                $final = array(2,3,4);

                return array_values(array_diff($final,$education_db->toArray()));

            }

        }
        else
        {
            $education_db = StudentEducation::where('enroll_no',$student['enroll_no'])->pluck('education_id');

            if(sizeof($education_db)==0)
            {

                $final = array(1,2,3,4);

                return $final;

            }else{

                $final = array(1,2,3,4);

                return array_values(array_diff($final,$education_db->toArray()));

            }

        }

    }

}
