<?php

namespace App\Http\Controllers;

use App\Application;
use App\Category;
use App\Education;
use App\Events\EmailNotification;
use App\Helper;
use App\Http\Requests\CreatePlacementCriteria;
use App\Http\Requests\CreatePlacementsPrimaryDetails;
use App\Http\Requests\CreatePlacementsOpenForDetails;
use App\Http\Requests\CreateReOpenRegistration;
use App\Http\Requests\CreateSelectionRoundsDetails;
use App\Http\Requests\CreateSelectStudentsRoundwise;
use App\Http\Requests\CreateStudentRegistration;
use App\Mail\SelectedForRound1Email;
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
use Illuminate\Support\Facades\Mail;

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

        $checkboxes = $request->only('openFor_checkbox');           //When i fetch " OPENFOR_CHECKBOX value " it should already be in array format and it contains id

        $input = $checkboxes['openFor_checkbox'];

        $placement = PlacementPrimary::where('placement_id',$placement_id)->first();

        if(sizeof($placement)==0)
        {

            return response("No Primary Details for this Placement!",200);

        }

        $open_for_db = PlacementOpenFor::where('placement_id',$placement_id)->get();

        if(sizeof($open_for_db)!=0)
        {

            return response('Already DB has entry!',200);

        }

        sort($input);

        $placement->categories()->sync($input);

        $openfor = PlacementOpenFor::where('placement_id',$placement_id)->get();

        if(!$openfor)
        {

            return Helper::apiError("Can't fetch open for details",null,404);

        }

        return $openfor;

    }

    public function getRoundNumber($user_id,$placement_id)
    {

        $placement = PlacementPrimary::where('placement_id',$placement_id)->first();

        if(sizeof($placement)==0)
        {

            return response("No Primary Details for this Placement!",200);

        }

        $last_selection_round = SelectionRound::where('placement_id',$placement_id)->latest()->first();

        if(sizeof($last_selection_round)==0)
        {

            return 1;

        }
        else
        {

            return $last_selection_round['round_no']+1;

        }

    }

    public function selectionRound(Request $request, $user_id, $placement_id)
    {

        $input = $request->only('round_no','round_name','round_description','date_of_round');

        $input['placement_id'] = $placement_id;

        $placement = PlacementPrimary::where('placement_id',$placement_id)->first();

        if(sizeof($placement)==0)
        {

            return response("No Primary Details for this Placement!",200);

        }

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

        $placement = PlacementPrimary::where('placement_id',$placement_id)->first();

        if(sizeof($placement)==0)
        {

            return response("No Primary Details for this Placement!",200);

        }

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

    public function openRegistrationForPlacement($user_id, $placement_id)           //start - change status to application
    {

        $placement_primary = PlacementPrimary::where('placement_id',$placement_id)->first();

        if(!$placement_primary)
        {
            Helper::apiError('No Details for such Placement ID', null, 404);
        }

        $placement_open_for = PlacementOpenFor::where('placement_id',$placement_id)->get();

        $placement_selection_round = SelectionRound::where('placement_id',$placement_id)->get();

        if(sizeof($placement_open_for) < 1 && sizeof($placement_selection_round) < 1)
        {

            return response("You haven't set open for and selection round, Can't start!",200);

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

    public function updateDateOfSelectionRound(Request $request,$user_id, $placement_id, $round_no)              //here to update the status and date of rounds.. as while creating not necessary they will insert that
    {
        //date_of_round , venue, date, time
        $input = $request->only('date_of_round');

        $venue_input = $request->only('venue');

        $venue = $venue_input['venue'];

        $date_input = $request->only('date');

        $date = $venue_input['venue'];

        $time_input = $request->only('venue');

        $time = $venue_input['venue'];

        $input = array_filter($input, function($value){

            return $value != null;

        });

        $round = SelectionRound::where('placement_id',$placement_id)->where('round_no',$round_no)->first();


        //send mass mail to all the students who have registered

        $students_in_application = Application::where('placement_id',$placement_id)->pluck('enroll_no');

        if(sizeof($students_in_application)==0)
        {

            $round->update($input);

            return $round;

        }

        if(!$students_in_application)
        {

            return Helper::apiError("No Student Found!",null,404);

        }


        $round->update($input);


//        $placement_primary = PlacementPrimary::with(['company'])->where('placement_id',$placement_id)->first();
//
//        if(!$placement_primary)
//        {
//
//            return Helper::apiError("No Placement Found with this id!",null,404);
//
//        }
//
//
//        $job_title = $placement_primary['job_title'];
//
//        $location = $placement_primary['location'];
//
//        $company_name = $placement_primary["company"]['company_name'];
//
//        $job_type = Job_Type::where('id',$placement_primary['job_type_id'])->pluck('job_type');
//
//        $job_type_name = $job_type[0];
//
//        $data = [
//
//            'job_title' => $job_title,
//            'location' => $location,
//            'company_name' => $company_name,
//            'job_type_name' => $job_type_name,
//            'round_no' => $round_no,
//            'round_name' => $round{'round_name'}
//            'round_date' => $date,
//            'round_time' => $time,
//            'venue' => $venue
//
//        ];
//
//
//        foreach ($students_in_application as $enroll_no)
//        {
//
//              Mail::to("$enroll_no@daiict.ac.in")->send(new EmailNotification($data));
//
//        }

        return $round;

    }


    public function selectStudentsFromApplication(Request $request, $user_id, $placement_id)         //starting from application layer - select checkboxes and thus data will come in array format
    {

        $students_from_applications = $request->only('students_from_applications_checkbox');                  //receiving enroll no

        $student_enroll_no_list = $students_from_applications['students_from_applications_checkbox'];

        $selectedStudents = [];

        $i = 0;

        $placement_primary = PlacementPrimary::with(['company'])->where('placement_id',$placement_id)->first();

        if(!$placement_primary)
        {

            return Helper::apiError("No Placement Found with this id!",null,404);

        }


        $job_title = $placement_primary['job_title'];

        $location = $placement_primary['location'];

        $company_name = $placement_primary["company"]['company_name'];

        $job_type = Job_Type::where('id',$placement_primary['job_type_id'])->pluck('job_type');

        $job_type_name = $job_type[0];

        $selection_round_detail = SelectionRound::where('placement_id',$placement_id)->where('round_no',1)->first();

        $data = [

            'job_title' => $job_title,
            'location' => $location,
            'company_name' => $company_name,
            'job_type_name' => $job_type_name,
            'round_name' => $selection_round_detail['round_name'],
        ];

        foreach ( $student_enroll_no_list as $student_enroll_no )
        {

            $input['placement_id'] = $placement_id;

            $input['enroll_no'] = $student_enroll_no;

            $input['round_no'] = 1;

            $student_in_db = SelectStudentRoundwise::where('placement_id', $input['placement_id'])->where('enroll_no',$input['enroll_no'])->where('round_no',$input['round_no'])->first();

            if(sizeof($student_in_db)!=0)
            {

                $selectedStudents[$i] = $student_in_db;

                $i++;

            }else{

                $selectedStudents[$i] = SelectStudentRoundwise::create($input);

                if(!$selectedStudents[$i])
                {

                    return Helper::apiError("Wasn't able to Insert!",null,404);

                }

                $data['round_no'] = 1;

                $i++;

            }

            //Mail::to("$student_enroll_no@daiict.ac.in")->send(new SelectedForRound1Email($data));

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

            if( is_null($student_enroll_no_list[0]) )
            {

                return Helper::apiError("No enroll no at first index",null,404);

            }

            $selection_round = [];

            $i = 0;

            $input['placement_id'] = $placement_id;

            foreach ( $student_enroll_no_list as $student_enroll_no )
            {

                $input['enroll_no'] = $student_enroll_no;

                $input['package'] = 0;

                $student_in_db = Offer::where('placement_id',$input['placement_id'])->where('enroll_no',$input['enroll_no'])->where('package',$input['package'])->first();

                if(sizeof($student_in_db)!=0)
                {

                    $selection_round[$i] = $student_in_db;

                    $i++;

                }else{

                    $selection_round[$i] = Offer::create($input);

                    $i++;

                }

            }

            return $selection_round;

        }

        $next_round_details = SelectionRound::where('placement_id',$placement_id)->where('round_no',$current_round+1)->first();

        $placement_primary = PlacementPrimary::with(['company'])->where('placement_id',$placement_id)->first();

        if(!$placement_primary)
        {

            return Helper::apiError("No Placement Found with this id!",null,404);

        }


        $job_title = $placement_primary['job_title'];

        $location = $placement_primary['location'];

        $company_name = $placement_primary["company"]['company_name'];

        $job_type = Job_Type::where('id',$placement_primary['job_type_id'])->pluck('job_type');

        $job_type_name = $job_type[0];

        $data = [

            'job_title' => $job_title,
            'location' => $location,
            'company_name' => $company_name,
            'job_type_name' => $job_type_name,
            'round_no' => $next_round_details['round_no'],
            'round_name' => $next_round_details['round_name'],

        ];

        $selection_round = [];

        $i = 0;

        foreach ( $student_enroll_no_list as $student_enroll_no )
        {

            $selection_round[$i] = SelectStudentRoundwise::where('enroll_no',$student_enroll_no)->where('placement_id',$placement_id)->first();

            $selection_round[$i]->update(array('round_no' => ($current_round + 1)));

            $i++;

            //Mail::to("$student_enroll_no@daiict.ac.in")->send(new SelectedForRound1Email($data));

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

        $placements->categories()->attach($input);

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
        'jobType', 'placementSelection', 'categories.criterias.education'])->find($placement_id);

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

        $students_in_round_details = SelectStudentRoundwise::where('placement_id',$placement_id)->distinct()->pluck('enroll_no');

        if(!$students_in_round_details)
        {
            return Helper::apiError("No Student Found in Round Layer!",null,404);
        }

        $students_in_application = Application::where('placement_id',$placement_id)->pluck('enroll_no');

        if(sizeof($students_in_application)==0)
        {
            return response("None has applied yet!",200);
        }

        $students = Student::with(['user','category'])->whereIn('enroll_no',$students_in_application)->get();

        if(sizeof($students)==0)
        {

            return Helper::apiError("Can't fetch Students!",null,404);

        }

        if(sizeof($students_in_round_details)==0)
        {
            return $students;
        }

        $array_diff = array_diff($students_in_application->toArray(),$students_in_round_details->toArray());

        $remaining_student = array_values($array_diff);

        if(sizeof($remaining_student)==0)
        {
            return response("All Students move to Rounds",200);
        }

        $students = Student::with(['user','category'])->whereIn('enroll_no',$remaining_student)->get();

        if(sizeof($students)==0)
        {

            return Helper::apiError("Can't fetch Students!",null,404);

        }

        return $students;

    }

    public function remainingStudentsRoundwise($user_id, $placement_id, $round_no)  //if about round1 then round1 me wo sare students jo round2 me move nai ho paye
    {

        $round_details = SelectionRound::where('placement_id',$placement_id)->pluck('round_no');

        $size = sizeof($round_details);

        if($round_no == $size)
        {

            $offers = Offer::where('placement_id',$placement_id)->pluck('enroll_no');

            $selection_round_current_details = SelectStudentRoundwise::where('placement_id',$placement_id)->where('round_no',$round_no)->pluck('enroll_no');

            if(sizeof($selection_round_current_details)==0 or is_null($selection_round_current_details) or !$selection_round_current_details)
            {
                return response("All Students of this round moved to Offer Layer!",200);
            }

            $remaining_enroll_nos = array_values(array_diff($selection_round_current_details->toArray(),$offers->toArray()));

            if(sizeof($remaining_enroll_nos)!=0)
            {

                $students = Student::with(['user','category'])->whereIn('enroll_no',$remaining_enroll_nos)->get();

                if(sizeof($students)==0)
                {

                    return Helper::apiError("Can't fetch Students!",null,404);

                }

                return $students;

            }

            return response("All Students in Last Round got Offer!",200);

        }

        $current_round = $round_no;

        $selection_round_current_details = SelectStudentRoundwise::where('placement_id',$placement_id)->where('round_no',$current_round)->pluck('enroll_no');

        if(sizeof($selection_round_current_details)==0 or is_null($selection_round_current_details) or !$selection_round_current_details)
        {
            return response("All Students of this round moved to next Round!",200);
        }

        $rounds_upto_now = [];

        for($i=1;$i<=$round_no;$i++)
        {

            array_push($rounds_upto_now,$i);

        }

        $round_after_now = array_values(array_diff($round_details->toArray(),$rounds_upto_now));

        $selection_round_next_details = SelectStudentRoundwise::where('placement_id',$placement_id)->whereIn('round_no',$round_after_now)->pluck('enroll_no');

        $students = Student::with(['user','category'])->whereIn('enroll_no',$selection_round_current_details)->get();

        if(sizeof($students)==0)
        {

            return Helper::apiError("Can't fetch Students!",null,404);

        }

        if(sizeof($selection_round_next_details)==0)
        {

            return $students;

        }

        $remaining_student = array_diff($selection_round_current_details->toArray(),$selection_round_next_details->toArray());

        $remaining_students = array_values($remaining_student);

        $students = Student::with(['user','category'])->whereIn('enroll_no',$remaining_students)->get();

        if(sizeof($students)==0)
        {

            return Helper::apiError("Can't fetch Students!",null,404);

        }

        return $students;

    }

    public function checkIfRoundsCompleted($user_id,$placement_id, $round_no)
    {

        $round_details = SelectionRound::where('placement_id',$placement_id)->pluck('round_no');

        $size = sizeof($round_details);

        if($round_no == $size)
        {
            return response("Selection Round Complete!",200);
        }

        return response("Selection Round Incomplete!",200);

    }

    public function remainingStudentsForOffer($user_id,$placement_id)
    {

        $students_offered = Offer::where('placement_id',$placement_id)->where('package','=',0)->pluck('enroll_no');

        if(sizeof($students_offered)==0)
        {
            return response("All Students in offer layer got Offer!",200);
        }

        $students = Student::with(['user','category'])->whereIn('enroll_no',$students_offered)->get();

        if(sizeof($students)==0)
        {

            return Helper::apiError("Can't fetch Students!",null,404);

        }

        return $students;

    }

    public function jobProfile($user_id)
    {

        $student = Student::where('user_id',$user_id)->first();

        if(!$student)
        {

            Helper::apiError("No Student Found!",null,404);

        }

        $category_id = $student['category_id'];

        $placement_primary = PlacementPrimary::with(['company','categories' => function($q) use ($category_id){
            $q->where('categories.id',$category_id);
        }])->where('status','!=','draft')->get();

        if(is_null($placement_primary))
        {

            return Helper::apiError("No placement Primary Found!",null,404);

        }

        if(sizeof($placement_primary)==0)
        {

            return Helper::apiError("No placement Primary Found!",null,404);

        }

        $placements = [];

        foreach ($placement_primary as $single)
        {

            if(sizeof($single["categories"]) != 0)
            {

                array_push($placements,$single);

            }

        }

        return $placements;

    }

    public function placementPrimaryAll($user_id)
    {

        $company = Company::where('user_id',$user_id)->first();

        if(!$company)
        {
            return Helper::apiError("Can't fetch Company!",null,404);
        }

        $company_id = $company['id'];

        $placements = PlacementPrimary::with(['company','placement_season','jobType'])->where('status','!=','draft')->where('company_id',$company_id)->latest()->get();

        if(!$placements)
        {
            return Helper::apiError("No placements!",null,404);
        }

        return $placements;

    }


    public function placementPrimaryAllWithStatusDraft($user_id)
    {

        $company = Company::where('user_id',$user_id)->first();

        if(!$company)
        {
            return Helper::apiError("Can't fetch Company!",null,404);
        }

        $company_id = $company['id'];

        $placements = PlacementPrimary::with(['company','placement_season','jobType'])->where('status','=','draft')->where('company_id',$company_id)->latest()->get();

        if(!$placements)
        {
            return Helper::apiError("No placements!",null,404);
        }

        return $placements;

    }


    public function remainingCategories($user_id,$placement_id,$category_id=0)
    {

        $open_for_details = PlacementOpenFor::where('placement_id',$placement_id)->orderBy('category_id','asc')->pluck('category_id');

        if(!$open_for_details)
        {
            return response("No Open For details!",200);
        }

        if($category_id==0)
        {

            $placements = PlacementCriteria::where('placement_id',$placement_id)->first();

            if(sizeof($placements)==0)
            {

                $open_for = Category::where('id',$open_for_details[0])->get();

                if(!$open_for)
                {
                    return Helper::apiError("Can't fetch Open for details",null,404);
                }

                return $open_for;

            }

            $latest_placement_wise = PlacementCriteria::where('placement_id',$placement_id)->pluck('category_id');

            $latest_placement_wise = array_reverse($latest_placement_wise->toArray());

            if(sizeof($latest_placement_wise)==0)
            {
                return Helper::apiError("Can't fetch placement",null,404);
            }

            $latest_placement_category = $latest_placement_wise[0];

            $check_if_four_entries = PlacementCriteria::where('placement_id',$placement_id)->where('category_id',$latest_placement_category)->get();

            if(sizeof($check_if_four_entries)==0)
            {
                return Helper::apiError("Error in fetching details",null,404);
            }

            if(sizeof($check_if_four_entries)>=4)
            {

                $entries_upto_now = [];

                for($i=1;$i<=$latest_placement_category;$i++)
                {
                    array_push($entries_upto_now,$i);
                }

                $entries_after_now = array_values(array_diff($open_for_details->toArray(),$entries_upto_now));

                if(sizeof($entries_after_now)==0)
                {
                    return response("Done!",200);
                }

                $open_for = Category::where('id',$entries_after_now[0])->get();

                if(!$open_for)
                {
                    return Helper::apiError("Can't fetch Open for details",null,404);
                }

                return $open_for;

            }

            $open_for = Category::where('id',$latest_placement_category)->get();

            if(!$open_for)
            {
                return Helper::apiError("Can't fetch Open for details",null,404);
            }

            return $open_for;

        }
        else
        {

            $open_for_last_entries = PlacementOpenFor::where('placement_id',$placement_id)->pluck('category_id');

            if(sizeof($open_for_last_entries)==0)
            {
                return Helper::apiError("can't fetch!",null,404);
            }

            $temp = array_reverse($open_for_last_entries->toArray());

            $last_entry = $temp[0];

            if($category_id==$last_entry)
            {
                return response("Done!",200);
            }

            $entries_upto_now = [];

            for ($i=1;$i<=$category_id;$i++)
            {
                array_push($entries_upto_now,$i);
            }

            $entries_after_now = array_values(array_diff($open_for_details->toArray(),$entries_upto_now));

            if(sizeof($entries_after_now)==0)
            {
                return response("Done!",200);
            }

            $next_entry = $entries_after_now[0];

            $open_for = Category::where('id',$next_entry)->get();

            if(!$open_for)
            {
                return Helper::apiError("Can't fetch Open for details",null,404);
            }

            return $open_for;

        }


    }

    public function remainingOpenFor($user_id,$placement_id)
    {

        $categories_detail_in_db = PlacementCriteria::where('placement_id',$placement_id)->get();

        $categories_in_db = $categories_detail_in_db->pluck('category_id');

        $openFor = PlacementOpenFor::where('placement_id',$placement_id)->pluck('category_id');

        if(!$openFor)
        {
            return response("No Open For Details mentioned yet!",200);
        }

        if(sizeof($categories_in_db)==0)
        {

            $open_for = Category::whereIn('id',$openFor)->get();

            if(!$open_for)
            {
                return Helper::apiError("Could not find Category Detail!",null,404);
            }

            return $open_for;

        }
        else
        {

            $remaining_categories = array_values(array_diff($openFor->toArray(),$categories_in_db->toArray()));

            if(sizeof($remaining_categories)==0)
            {

                return response("Done with All Categories!",200);

            }

            $open_for = Category::whereIn('id',$remaining_categories)->get();

            if(!$open_for)
            {
                return Helper::apiError("Could not find Category Detail!",null,404);
            }

            return $open_for;

        }

    }

    public function remainingEducation($user_id,$placement_id,$category_id)
    {

        $educations = Education::all();

        if(!$educations)
        {
            return Helper::apiError("Could not find Education Id!",null,404);
        }

        $education_ids = $educations->pluck('id');

        $criterias = PlacementCriteria::where('placement_id',$placement_id)->where('category_id',$category_id)->pluck('education_id');

        if(sizeof($criterias)==0)
        {

            if($category_id==2)
            {

                unset($education_ids[0]);

                $education = Education::whereIn('id',$education_ids)->get();

                if(!$education)
                {

                    return Helper::apiError("Could not find education!",null,404);

                }

                return $education;

            }

            return $educations;

        }
        else
        {

            if($category_id==2)         //checking for btech
            {

                unset($education_ids[0]);

            }

           $remaining_educations = array_values(array_diff($education_ids->toArray(),$criterias->toArray()));

           if(sizeof($remaining_educations)==0)
           {

               return response("Done with Educations!",200);

           }
           else
           {

               $education = Education::whereIn('id',$remaining_educations)->get();

               if(!$education)
               {

                   return Helper::apiError("Could not find education!",null,404);

               }

               return $education;

           }

        }

    }
    
    public function getRemainingOpenFor($user_id,$placement_id)
    {

        $all_open_for = Category::all();

        if(!$all_open_for)
        {
            return Helper::apiError("Cannot find Open For Detail!",null,404);
        }

        $current_open_for = PlacementOpenFor::where('placement_id',$placement_id)->pluck('category_id');

        if(sizeof($current_open_for)==0)
        {

            return $all_open_for;

        }

        $already_open_for = Category::whereNotIn('id',$current_open_for)->get();

        if(sizeof($already_open_for)==0)
        {
            return response("No Category Left!",200);
        }

        return $already_open_for;
        
    }

    public function getDraftPlacements($user_id)
    {

        $company = Company::where('user_id',$user_id)->pluck('id');

        if(!$company)
        {
            return Helper::apiError("Could not find Company!",null,404);
        }

        $placements = PlacementPrimary::with(['placement_season','jobType'])->where('company_id',$company[0])->where('status','draft')->get();

        if(sizeof($placements)==0)
        {
            return response("No Placements!",200);
        }

        return $placements;

    }

}
