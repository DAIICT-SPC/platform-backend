<?php

namespace App\Http\Controllers;

use App\Application;
use App\Category;
use App\Helper;
use App\Http\Requests\CreatePlacementCriteria;
use App\Http\Requests\CreatePlacementsPrimaryDetails;
use App\Http\Requests\CreatePlacementsOpenForDetails;
use App\Http\Requests\CreateReOpenRegistration;
use App\Http\Requests\CreateSelectionRoundsDetails;
use App\Http\Requests\CreateSelectStudentsRoundwise;
use App\Http\Requests\CreateStudentRegistration;
use App\Offer;
use App\PlacementCriteria;
use App\SelectStudentRoundwise;
use App\StudentEducation;
use App\User;
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

    public function createPlacementDrive(CreatePlacementsPrimaryDetails $request, $user_id = null)
    {

        $input = $request->only('job_title','job_description','last_date_for_registration','location','no_of_students','package','job_type_id');

        if (is_null($user_id)) {

            $company_details = request()->user()->company;

        } else {

            $company_details = User::find($user_id)->company;

        }

        if(!$company_details){

            return Helper::apiError('No such Entry found for Company!',null,404);

        }

        $company_id = $company_details['id'];

        $input['company_id'] = $company_id;

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

    public function reOpenRegistration(CreateReOpenRegistration $request, $user_id, $placement_id)
    {

        $input = $request->only('last_date_for_registration');

        $placement_primary = PlacementPrimary::where('placement_id',$placement_id)->first();

        if(!$placement_primary) {

            Helper::apiError('No Details for such Placement ID', null, 404);

        }

        PlacementPrimary::where('placement_id',$placement_id)->update( array('last_date_for_registration' => $input['last_date_for_registration']));

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

        $i = 0;

        foreach ($openFor as $category)             //fetching all names
        {

            $category_name = DB::table('categories')->where('id',$category['category_id'])->value('name');

            $category_names[$i] = $category_name;

            $i++;

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
                    // Mail to Students
                }

            }

        }

        return "Successfully sent mail to all students";

    }


    public function updateDateOfSelectionRound(Request $request, $placement_id, $round_no)              //here to update the status and date of rounds.. as while creating not necessary they will insert that
    {

        $input = $request->only('date_of_round');

        $input = array_filter($input, function($value){
            return $value != null;
        });

        $round = SelectionRound::where('placement_id',$placement_id)->where('round_no',$round_no)->first();

        //send mass mail to all the students who have registered
        // MAIL TO

        $round->update($input);

        return $round;

    }


    public function selectStudentsFromApplication(Request $request, $user_id, $placement_id)         //starting from application layer - select checkboxes and thus data will come in array format
    {

        $students_from_applications = $request->only('students_from_applications_checkbox');                  //receiving enroll no

        $student_enroll_no_list = $students_from_applications['students_from_applications_checkbox'];

        $selectedStudents = [];

        $i = 0;

        foreach ( $student_enroll_no_list as $student_enroll_no )
        {

            $input['placement_id'] = $placement_id;

            $input['enroll_no'] = $student_enroll_no;

            $input['round_no'] = 1;

            $selectedStudents[$i] = SelectStudentRoundwise::create($input);

            if(!$selectedStudents[$i])
            {

                return Helper::apiError("Wasn't able to Insert $selectedStudents[$i]",null,404);

            }

            $i++;

        }

        return $selectedStudents;

    }

    public function selectStudentsRoundwise(Request $request, $user_id, $placement_id)            //select_students_roundwise - must be coming in form of checkbox
    {

        $students_roundwise = $request->only('student_roundwise');                  //receiving enroll no

        $student_enroll_no_list = $students_roundwise['student_roundwise'];

        $round_details = SelectionRound::where('placement_id',$placement_id)->get();

        $no_of_rounds = sizeof($round_details);

        if( is_null($student_enroll_no_list[0]) )
        {

            return Helper::apiError("No enroll no at first index",null,404);

        }

        $selection_round_currently = SelectStudentRoundwise::where('placement_id',$placement_id)->where('enroll_no',$student_enroll_no_list[0])->first();

        $current_round = $selection_round_currently['round_no'];

        if( $current_round == $no_of_rounds)
        {

            return Helper::apiError("Rounds Completed already!",null,402);

        }

        $selection_round = [];

        $i = 0;

        foreach ( $student_enroll_no_list as $student_enroll_no )
        {

            $selection_round[$i] = SelectStudentRoundwise::where('enroll_no',$student_enroll_no)->where('placement_id',$placement_id)->first();

            $selection_round[$i]->update(array('round_no' => ($current_round + 1)));

            $i++;

        }

        return $selection_round;

    }


    public function update()                                    //Update anything PlacementsPrimary, PlacementsOpenFor, Placements
    {

    }

    public function placementsAll(Request $request)
    {

        $status = $request->only('status');

        $open_for = $request->only('open_for');

        $location = $request->only('location');

        if( is_null($status['status']) && is_null($open_for['open_for']) && is_null($location['location']))
        {

            $placements = DB::table('placements_primary')
                ->leftjoin('placements_open_for', 'placements_primary.placement_id', '=', 'placements_open_for.placement_id')
                ->leftjoin('placement_criterias', 'placements_primary.placement_id', '=', 'placement_criterias.placement_id')
                ->leftjoin('selection_rounds', 'placements_primary.placement_id', '=', 'selection_rounds.placement_id')
                ->join('education', 'placement_criterias.education_id', '=', 'education.id')
                ->join('categories', 'placement_criterias.category_id', '=', 'categories.id')
                ->leftjoin('job_types', 'placements_primary.job_type_id', '=', 'job_types.id')
                ->select('placements_primary.*','job_types.job_type','job_types.duration','categories.name','selection_rounds.*','education.name')
                ->whereIn('status',['application','closed'])
                ->distinct()
                ->get();

            return $placements;

        }

    }

    public function updateSelectionRound(Request $request, $placement_id, $round_no)              //here to update the status and date of rounds.. as while creating not necessary they will insert that
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
