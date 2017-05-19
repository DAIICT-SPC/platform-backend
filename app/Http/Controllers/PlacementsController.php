<?php

namespace App\Http\Controllers;

use App\Application;
use App\Category;
use App\Helper;
use App\Http\Requests\CreatePlacementCriteria;
use App\Http\Requests\CreatePlacementsPrimaryDetails;
use App\Http\Requests\CreatePlacementsOpenForDetails;
use App\Http\Requests\CreateSelectionRoundsDetails;
use App\Http\Requests\CreateStudentRegistration;
use App\PlacementCriteria;
use App\SelectStudentRoundwise;
use App\StudentEducation;
use Illuminate\Http\Request;
use App\PlacementPrimary;
use App\PlacementOpenFor;
use App\SelectionRound;
use App\Job_Type;
use App\Company;
use App\Student;
use Illuminate\Support\Facades\DB;

class PlacementsController extends Controller
{

    public function getPlacementPrimary($placement_id)
    {
        $placement = PlacementPrimary::where('placement_id',$placement_id)->first();

        if(!$placement)
        {
            Helper::apiError("No Placement Found");
        }

        return $placement;
    }

    public function createPlacementDrive(CreatePlacementsPrimaryDetails $request, $user_id)
    {

        $input = $request->only('job_title','job_description','last_date_for_registration','location','no_of_students','package','job_type_id');

        $company_details = Company::where('user_id',$user_id)->first();

        if(!$company_details)
        {
            Helper::apiError('No company has such user id!',null,404);
        }

        $company_id = $company_details['id'];

        $input['company_id'] = $company_id;

        //instead of above magajmari, i simply can do the following
        //to fetch company id -> $request->user() so that current user will be known and then adding its id
        //$input['company_id'] = $request->user()->id();

        $placement_primary =  PlacementPrimary::create($input);

        if(!$placement_primary){
            Helper::apiError("Can't create new Placement Drive",null,404);
        }

        return $placement_primary;

    }

    public function placementDriveOpenFor(Request $request, $placement_id)
    {

        $checkboxes = $request->input('openFor_checkbox');           //When i fetch " OPENFOR_CHECKBOX value " it should already be in array format and it contains id

        $openfor = null;

        $i=0;

        foreach ($checkboxes as $checkbox)
        {

            $input = [];

            $input['category_id'] = $checkbox;

            $input['placement_id'] = $placement_id;

            $openfor[$i] = PlacementOpenFor::create($input);

            $i++;

         }

        return $openfor;

    }

    public function selectionRound(Request $request, $user_id,$placement_id)
    {

        $input = $request->only('round_no','round_name','round_description','date_of_round');

        $input['placement_id'] = $placement_id;

        $selectionRound = SelectionRound::create($input);

        if(!$selectionRound)
        {
            Helper::apiError('Selection Round cannot be created',null,404);
        }

        return $selectionRound;

    }

    public function setPlacementCriteria(CreatePlacementCriteria $request, $user_id, $placement_id)       //have a - set button and new button - on the first try show daiict (masters)
    {
        $input = $request->only('education_id', 'cpi_required');

        $input['placement_id'] = $placement_id;

        $placement_criteria = PlacementCriteria::create($input);

        return $placement_criteria;

    }

    public function openRegistrationForPlacement($user_id, $placement_id)
    {

        $placement_primary = PlacementPrimary::where('placement_id',$placement_id)->first();

        if(!$placement_primary)
        {
            Helper::apiError('No Details for such Placement ID', null, 404);
        }

        PlacementPrimary::where('placement_id', $placement_id)->update(array('status' => 'application'));

        $placement_primary = PlacementPrimary::where('placement_id',$placement_id)->first();

        return $placement_primary;

    }

    public function closeRegistrationForPlacement($user_id, $placement_id)
    {

        $placement_primary = PlacementPrimary::where('placement_id',$placement_id)->first();

        if(!$placement_primary)
        {
            Helper::apiError('No Details for such Placement ID', null, 404);
        }

        PlacementPrimary::where('placement_id', $placement_id)->update(array('status' => 'closed'));

        $placement_primary = PlacementPrimary::where('placement_id',$placement_id)->first();

        return $placement_primary;

    }



    public function showAllSelectionRound($placement_id)
    {

        $selection_rounds = SelectionRound::where('placement_id',$placement_id)->get();

        if(!$selection_rounds)
        {
            Helper::apiError('Cant show selection round!',null,404);
        }

        return $selection_rounds;

    }

    public function showPlacement($placement_id)
    {

        $primary = PlacementPrimary::where('placement_id',$placement_id)->first();

        if(!$primary)
        {
            Helper::apiError('No Placement Drive with such ID',null,404);
        }

        $companyName = DB::table('companys')->where('id', $primary['company_id'])->value('company_name');       //fetching company name from company_id

        $primary['company_name'] = $companyName;

        $typeName = DB::table('job_types')->where('id', $primary['job_type_id'])->value('job_type');

        $primary['type_name'] = $typeName;


        $openFor = PlacementOpenFor::where('placement_id',$placement_id)->get();

        if(!$openFor)
        {
            Helper::apiError('No data for Open for Categories!',null,404);
        }

        $category_names = null;

        foreach ($openFor as $category)             //fetching all names
        {

            $category_name = DB::table('categories')->where('id',$category['category_id'])->value('name');

            $category_names[$category_name] = $category_name;

        }

        $selectionRound = SelectionRound::where('placement_id',$placement_id)->get();

        if(!$selectionRound)
        {
            Helper::apiError('No Selection Round Details!',null,404);
        }

        $placement['primary'] = $primary;
        $placement['openFor'] = $category_names;
        $placement['selectionRound'] = $selectionRound;

        return $placement;

    }

    public function showPlacementsPrimary($placement_id)
    {

        $placement_primary = PlacementPrimary::where('placement_id',$placement_id)->first();

        if(!$placement_primary)
        {
            Helper::apiError('No Details for such Placement ID', null, 404);
        }

        return $placement_primary;

    }

    public function showOpenForCategories($placement_id)            //finding ALL OPENFORDETAILS  -  To send them updates on dashboard and via mail
    {

        $openFor = PlacementOpenFor::where('placement_id',$placement_id)->get();

        if(!$openFor)
        {
            Helper::apiError('No Open For Details mentioned yet!',null,404);
        }

        return $openFor;

    }

    public function categoryWisePlacementMail($placement_id)             //to send them mail
    {

        $openFor = $this->showOpenForCategories($placement_id);

        // foreach category find all students that belong to that category and send them mail one by one

        foreach ($openFor as $aa)
        {

            if($aa != null)
            {
                $studentsBelongingToCategory = Student::where('category_id',$aa['category_id'])->get();

                foreach ($studentsBelongingToCategory as $student)
                {
                    // Mail to Students                                                                             -------------
                }

            }

        }

        return "Successfully sent mail to all students";

    }

    public function studentRegistration(CreateStudentRegistration $request,$user_id)          //student registering - Application giving layer - have to validate each student if its eligible or not
    {

        $student = Student::where('user_id',$user_id)->first();

        if(!$student)
        {
            Helper::apiError('No such Student Exist!',null,404);
        }

        $enroll_no = $student['enroll_no'];

        $placement_id = $request->only('placement_id');

        $input = $request->only('placement_id');

        $input['student_id'] = $student['id'];

        $criterias = PlacementCriteria::where('placement_id',$placement_id)->get();

        $student_education_list = StudentEducation::where('enroll_no',$enroll_no)->get();

        $i = 0; $j = 0;

        foreach ($criterias as $criteria)
        {

            $i++;

            foreach ($student_education_list as $student_education)
            {

                if($student_education['education_id'] == $criteria['education_id'])
                {

                    if($student_education['cpi'] >= $criteria['cpi_required'])
                    {
                        $j++;
                    }

                }

            }

        }

        //Check for Offer letter

        if($i == $j)
        {

            $application = Application::create($input);

            return $application;

        }else{

            return Helper::apiError('Sorry Your Application cant be accepted. You are not eligible!',null,402);

        }

    }

    public function showAllApplications($user_id,$placement_id)           //Searched by Company as who all have registered.. Also Filter according to their CPI
    {

        $applications = Application::where('placement_id',$placement_id)->get();

        $student_detail[] = null;

        $i = 0;

        foreach ($applications as $application)
        {

            $student_primary = Student::find($application['student_id']);

            $enroll_no = $student_primary['enroll_no'];

            $student_education = StudentEducation::where('enroll_no',$enroll_no)->get();

            $student['student_primary'] = $student_primary;

            $student['student_education'] = $student_education;

            $student_detail[$i] = $student;

            $i++;

        }

        return $student_detail;

    }

    public function selectStudentsFromApplication(Request $request, $user_id, $placement_id)         //starting from application layer - select checkboxes and thus data will come in array format
    {

        //mail everytime student reaches to next round

    }

    public function selectStudentsRoundwise()            //select_students_roundwise
    {

    }

    public function offerLetter()
    {

    }

    public function update()                                    //Update anything PlacementsPrimary, PlacementsOpenFor, Placements
    {

    }


    public function updateDateOfSelectionRound(Request $request, $placement_id, $round_no)              //here to update the status and date of rounds.. as while creating not necessary they will insert that
    {

        $input = $request->only('round_no','round_name','round_description','date_of_round');

        $input = array_filter($input, function($value){
            return $value != null;
        });

        $round = SelectionRound::where('placement_id',$placement_id)->where('round_no',$round_no)->first();

        //send mass mail to all the students who have registered
        // MAIL TO

        $round->update($input);

        return $round;

    }

}
