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
use App\PlacementSeason;
use App\PlacementSeason_Company;
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
        $placement = PlacementPrimary::with(['company','placement_season'])->where('placement_id',$placement_id)->first();

        if(!$placement)
        {
            Helper::apiError("No Placement Found");
        }

        return $placement;
    }

    public function createPlacementDrive(CreatePlacementsPrimaryDetails $request, $user_id = null)
    {

        $input = $request->only('job_title','job_description','last_date_for_registration','location','no_of_students','package','job_type_id','placement_season_id');

        if (is_null($user_id)) {

            $company_details = request()->user()->company;

        } else {

            $company_details = User::find($user_id)->company;

        }

        if(!$company_details){

            return Helper::apiError('No such Entry found for Company!',null,404);

        }

        $company_id = $company_details['id'];

        $placement_season = PlacementSeason::where('id',$input['placement_season_id'])->first();

        if($placement_season['status'] == 'draft')
        {
            return Helper::apiError("Placement Season has not started yet!",null,404);
        }

        if($placement_season['status' == 'closed'])
        {
            return Helper::apiError("Placement Season has got Closed!",null,404);
        }

        $allowed_in_placement_season = PlacementSeason_Company::where('company_id',$company_id)->where('placement_season_id',$input['placement_season_id'])->get();

        if(sizeof($allowed_in_placement_season) == 0)
        {

            return response("You are unauthorised to create Placement Drive in this placement season",402);

        }

        $input['company_id'] = $company_id;

        $placement_primary =  PlacementPrimary::create($input);

        if(!$placement_primary){

            Helper::apiError("Can't create new Placement Drive",null,404);

        }

        return $placement_primary;

    }

    public function placementDriveOpenFor(Request $request, $user_id, $placement_id)
    {

        $checkboxes = $request->input('openFor_checkbox');           //When i fetch " OPENFOR_CHECKBOX value " it should already be in array format and it contains id

        $openfor = null;

        $i=0;

        foreach ($checkboxes as $checkbox)
        {

            $input = [];

            $input['category_id'] = $checkbox;

            $input['placement_id'] = $placement_id;

            $already_in_db = PlacementOpenFor::where('placement_id',$placement_id)->where('category_id',$checkbox)->first();

            if( !is_null($already_in_db))
            {

                $openfor[$i] = PlacementOpenFor::create($input);

                $i++;

            }

            $openfor[$i] = $already_in_db;

            $i++;

         }

        return $openfor;

    }

    public function selectionRound(Request $request, $user_id, $placement_id)
    {

        $input = $request->only('round_no','round_name','round_description','date_of_round');

        $input['placement_id'] = $placement_id;

        $already_in_db = SelectionRound::where('placement_id',$placement_id)->where('round_no',$input['round_no'])->first();

        if( !is_null($already_in_db))
        {
            return $already_in_db;
        }

        $selectionRound = SelectionRound::create($input);

        if(!$selectionRound)
        {
            Helper::apiError('Selection Round cannot be created',null,404);
        }

        return $selectionRound;

    }

    public function setPlacementCriteria(CreatePlacementCriteria $request, $user_id, $placement_id)       //have a - set button and new button - on the first try show daiict (masters)
    {
        $input = $request->only('education_id', 'category_id', 'cpi_required');

        $input['placement_id'] = $placement_id;

        $already_in_db = PlacementCriteria::where('placement_id',$placement_id)->where('category_id',$input['category_id'])->where('education_id',$input['education_id'])->first();

        if(!is_null($already_in_db))
        {
            return $already_in_db;
        }

        $placement_criteria = PlacementCriteria::create($input);

        if(!$placement_criteria)
        {
            Helper::apiError("Cannot create Placement Criteria!",null,404);
        }

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

        PlacementPrimary::where('placement_id',$placement_id)->update( array('last_date_for_registration' => $input['last_date_for_registration'], 'status' => 'application'));

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

    public function showAllSelectionRound($user_id, $placement_id)
    {

        $selection_rounds = SelectionRound::where('placement_id',$placement_id)->get();

        if(!$selection_rounds)
        {
            Helper::apiError('Cant show selection round!',null,404);
        }

        return $selection_rounds;

    }

    public function showPlacementsPrimary($user_id, $placement_id)
    {

        $placement_primary = PlacementPrimary::where('placement_id',$placement_id)->first();

        if(!$placement_primary)
        {
            Helper::apiError('No Details for such Placement ID', null, 404);
        }

        return $placement_primary;

    }

    public function showOpenForCategories($user_id, $placement_id)            //finding ALL OPENFORDETAILS  -  To send them updates on dashboard and via mail
    {

        $openFor = PlacementOpenFor::where('placement_id',$placement_id)->get();

        if(!$openFor)
        {
            Helper::apiError('No Open For Details mentioned yet!',null,404);
        }

        return $openFor;

    }

    public function categoryWisePlacementMail($user_id, $placement_id)             //to send them mail
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


    public function updateDateOfSelectionRound(Request $request,$user_id, $placement_id, $round_no)              //here to update the status and date of rounds.. as while creating not necessary they will insert that
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

    public function updatePlacementsPrimary(Request $request, $user_id, $placement_id)                                    //Update anything PlacementsPrimary, PlacementsOpenFor, Placements
    {

        $identity = User::where('id',$user_id)->first();

        $role =  $identity["role"];

        if( $role == 'company')
        {
            $placements = PlacementPrimary::find($placement_id);

            $company = Company::where('user_id',$user_id)->first();

            if( $company->id != $placements['company_id']){

                return Helper::apiError("Unauthorized access",null,401);

            }

        }

        $input = $request->only('job_title','job_description','location','no_of_students','package','job_type_id', 'last_date_of_registration','placement_season_id');

        $input = array_filter($input, function($value){

            return $value != null;

        });

        $placements = PlacementPrimary::find($placement_id);

        $placements->update($input);

        return $placements;

    }

    public function updateOpenFor(Request $request,$user_id, $placement_id)
    {

        $identity = User::where('id',$user_id)->first();

        $role =  $identity["role"];

        if( $role == 'company')
        {
            $placements = PlacementPrimary::find($placement_id);

            $company = Company::where('user_id',$user_id)->first();

            if( $company->id != $placements['company_id']){

                return Helper::apiError("Unauthorized access",null,401);

            }

        }

        $placements = PlacementPrimary::find($placement_id);

        $input_array = $request->only('update_open_for');

        $input = $input_array['update_open_for'];

        $placements->categories()->sync($input);

        foreach ($input as $single)
        {

            PlacementCriteria::where('placement_id',$placement_id)->where('category_id','!=',$single)->delete();

        }

        $placement_open_for = PlacementOpenFor::where('placement_id',$placement_id)->get();

        return $placement_open_for;

    }

    public function updateCriteria(Request $request, $user_id, $placement_id)
    {

        $identity = User::where('id',$user_id)->first();

        $role =  $identity["role"];

        if( $role == 'company')
        {
            $placements = PlacementPrimary::find($placement_id);

            $company = Company::where('user_id',$user_id)->first();

            if( $company->id != $placements['company_id']){

                return Helper::apiError("Unauthorized access",null,401);

            }

        }

        $input = $request->only('education_id', 'category_id', 'cpi_required');

        $placement_criteria = PlacementCriteria::where('placement_id',$placement_id)->where('category_id',$input['category_id'])->where('education_id',$input['education_id'])->first();

        if(!$placement_criteria)
        {
            Helper::apiError("No such Criteria found!",null,404);
        }

        $placement_criteria->update($input);

        return $placement_criteria;

    }

    public function updateSelectionRound(Request $request, $user_id, $placement_id)
    {

        $identity = User::where('id',$user_id)->first();

        $role =  $identity["role"];

        if( $role == 'company')
        {
            $placements = PlacementPrimary::find($placement_id);

            $company = Company::where('user_id',$user_id)->first();

            if( $company->id != $placements['company_id']){

                return Helper::apiError("Unauthorized access",null,401);

            }

        }

        $input = $request->only('round_no','round_name','round_description');

        $input = array_filter($input, function($value){
            return $value != null;
        });

        $round = SelectionRound::where('placement_id',$placement_id)->where('round_no',$input['round_no'])->first();

        $round->update($input);

        return $round;

    }

    public function showPlacementDetails($user_id, $placement_id)
    {

        $placement = PlacementPrimary::with(['company', 'placement_season', 'categories.criterias' => function($q) use ($placement_id) {
            $q->where('placement_id', $placement_id);
        },
        'jobType', 'placementSelection'])->find($placement_id);

        if(!$placement)
        {
            Helper::apiError('No Placement Drive with such ID',null,404);
        }

        return $placement;

    }

    public function showStudentsInRound($user_id, $placement_id, $round_no)
    {

        $students = SelectStudentRoundwise::where('placement_id',$placement_id)->where('round_no',$round_no)->get();

        if(!$students)
        {
            return Helper::apiError("No Students in this round!",null,404);
        }

        return $students;

    }

    public function remainingStudentsInApplication($user_id, $placement_id)
    {

        $round_details = SelectionRound::where('placement_id',$placement_id)->first();

        $round_no = $round_details['round_no'];

        $students_in_round_details = SelectStudentRoundwise::where('placement_id',$placement_id)->where('round_no',$round_no)->get();

        if(!$students_in_round_details)
        {
            return Helper::apiError("No Student Found in Round Layer!",null,404);
        }

        if(sizeof($students_in_round_details)==0)
        {
            return response("All Students in Application Layer only!",200);
        }

        $student_round = [];

        foreach ($students_in_round_details as $student_in_round)
        {

            array_push($student_round,$student_in_round['enroll_no']);

        }

        $students_in_application = Application::where('placement_id',$placement_id)->get();

        $student_application = [];

        foreach ($students_in_application as $student_in_application)
        {

            array_push($student_application,$student_in_application['enroll_no']);

        }

        $array_diff = array_diff($student_application,$student_round);

        $remaining_student = array_values($array_diff);

        if(sizeof($remaining_student)==0)
        {
            return response("All Students move to Rounds",200);
        }

        return $remaining_student;

    }

    public function remainingStudentsRoundwise($user_id, $placement_id, $round_no)  //if about round1 then round1 me wo sare students jo round2 me move nai ho paye
    {

        $round_details = SelectionRound::where('placement_id',$placement_id)->get();

        $size = sizeof($round_details);

        if($round_no == $size)
        {
            return Helper::apiError("Already in Last Round plz check in remainingStudentsForOffer!",null,404);
        }

        $current_round = null;

        $next_round = null;

        foreach ($round_details as $round)
        {

            if( $round_no == $round['round_no'])
            {

                $current_round = $round;

            }

            if( ($round_no+1) == $round['round_no'])
            {

                $next_round = $round;

            }

        }

        $selection_round_current_details = SelectStudentRoundwise::where('placement_id',$placement_id)->where('round_no',$current_round['round_no'])->get();

        $selection_round_next_details = SelectStudentRoundwise::where('placement_id',$placement_id)->where('round_no',$next_round['round_no'])->get();

        if(sizeof($selection_round_next_details)==0)
        {
            return Helper::apiError("All Students in Current Round itself!",null,404);
        }

        $current_round_students = [];

        foreach ($selection_round_current_details as $selection_round_current)
        {

            array_push($current_round_students, $selection_round_current);

        }

        $next_round_students = [];

        foreach ($selection_round_next_details as $selection_round_next)
        {

            array_push($next_round_students, $selection_round_next);

        }

        $remaining_students = array_diff($current_round_students,$next_round_students);

        return $remaining_students;

    }

}
