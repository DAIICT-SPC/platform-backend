<?php


    Route::post('/authenticate', ['uses' => 'AuthController@authenticate']);

    Route::get('/', function (Request $request){

        $token = \JWTAuth::getToken();

        $user = \JWTAuth::toUser($token);

        return $user;

        //instead use

        //request()->user()

    })->middleware('jwt');

//
//Route::middleware('auth:api')->get('/user', function (Request $request) {
//    return $request->user();
//});





    Route::group(["prefix"=>'activation'], function() {

        Route::post('/single', ['uses' => 'ActivationController@createSingleEntry']);

        Route::post('/file', ['uses' => 'ActivationController@createViaFile']);

        Route::get('/activate/{code}' , ['uses' => 'ActivationController@findCode']);

        //Deletion of code is done when user is created hence refer to UsersController@registerUser - users/registerUser

    });

    Route::group(["prefix"=>'education'], function() {

        Route::get('/' , ['uses' => 'EducationController@index']);

        Route::post('/', ['uses' => 'EducationController@createNew']);

        Route::patch('/{id}', ['uses' => 'EducationController@updateEducation']);

        Route::delete('/{id}' , ['uses' => 'EducationController@destroy']);

    });


Route::group(["prefix"=>'users'], function() {

        Route::get('/', ['uses' => 'UsersController@index','middleware'=>'jwt']);

        Route::post('/registerUser', ['uses' => 'UsersController@registerUser']);

        Route::get('/{id}', ['uses' => 'UsersController@show']);

        Route::delete('/{user_id}', ['uses' => 'UsersController@destroy']);


    Route::group(['prefix'=>'/{user_id?}/student'],function(){

        Route::get('/', ['uses' => 'StudentsController@show']);         //It will get the student entry from student table

        Route::patch('/updatePersonal', ['uses' => 'UsersController@update']);      //It will update details like email,username,password which are present in "USERS" table

        Route::patch('/update', ['uses' => 'StudentsController@update']);

        Route::post('/previousEducation', ['uses' => 'StudentsController@storePreviousEducation']);

        Route::get('/previousEducation', ['uses' => 'StudentsController@fetchPreviousEducation']);

        Route::patch('/update/previousEducation/{id}', ['uses' => 'StudentsController@updateEducation']);

        Route::post('/project', ['uses' => 'StudentsController@storeProjects']);

        Route::get('/project', ['uses' => 'StudentsController@fetchProjects']);

        Route::post('/internship', ['uses' => 'StudentsController@storeInternship']);

        Route::get('/internship', ['uses' => 'StudentsController@fetchInternships']);

        Route::post('/placementRegistration', ['uses' => 'PlacementApplicationController@studentRegistration']);

        Route::post('/cancelRegistration', ['uses' => 'PlacementApplicationController@cancelRegistration']);

        Route::get('/dashboard', ['uses' => 'StudentsController@dashboard']);

        Route::post('/education', ['uses' => 'StudentsController@storeStudentEducation']);

        Route::get('/education', ['uses' => 'StudentsController@fetchEducation']);

        Route::patch('/education/{education_id}', ['uses' => 'StudentsController@updateEducation']);

        Route::post('/uploadResume', ['uses' => 'StudentsController@uploadResume']);

        Route::get('/getResume', ['uses' => 'StudentsController@getResume']);


        Route::post('/eligibility', ['uses' => 'StudentsController@eligibility']);



    });


    Route::group(['prefix'=>'/{user_id?}/company'],function(){

        Route::get('/', ['uses' => 'CompanysController@show']);         //It will get the Company entry from company table

        Route::patch('/updatePersonal', ['uses' => 'UsersController@update']);          //It will update details like email,username,password which are present in "USERS" table

        Route::patch('/update', ['uses' => 'CompanysController@update']);

        Route::post('/createPlacement', ['uses' => 'PlacementsController@createPlacementDrive']);

        Route::post('/{placement_id}/setSelectionRound', ['uses' => 'PlacementsController@selectionRound']);

        Route::get('/{placement_id}/showOpenFor', ['uses' => 'PlacementsController@showOpenForCategories']);    //for entries combo box in placement criteria page

        Route::post('/{placement_id}/setPlacementCriteria', ['uses' => 'PlacementsController@setPlacementCriteria']);

        Route::get('/placement/{placement_id}', ['uses' => 'PlacementApplicationController@showAllApplications']);

        Route::post('/{placement_id}/setOpenForDetails', ['uses' => 'PlacementsController@placementDriveOpenFor']);

        Route::post('/{placement_id}/openRegistrationForPlacement', ['uses' => 'PlacementsController@openRegistrationForPlacement']);

        Route::post('/{placement_id}/closeRegistrationForPlacement', ['uses' => 'PlacementsController@closeRegistrationForPlacement']);

        Route::patch('/{placement_id}/update/{round_no}', ['uses' => 'PlacementsController@updateDateOfSelectionRound']);

        Route::get('/{student_id}/getResume', ['uses' => 'StudentsController@getResume']);

        Route::post('/{placement_id}/giveOffer', [ 'uses' => 'PlacementOffersController@giveOfferLetter' ]);

        Route::post('/{placement_id}/cancelOffer', [ 'uses' => 'PlacementOffersController@cancelOfferLetter' ]);

        Route::post('/{placement_id}/reOpenRegistration', ['uses' => 'PlacementsController@reOpenRegistration']);

        Route::post('/{placement_id}/selectStudentsFromApplication', ['uses' => 'PlacementsController@selectStudentsFromApplication']);

        Route::post('/{placement_id}/selectStudentsRoundwise', ['uses' => 'PlacementsController@selectStudentsRoundwise']);

    });

    Route::group(['prefix'=>'/{user_id}/admin'],function(){

        Route::get('/', ['uses' => 'AdminsController@show']);         //It will get the Company entry from company table

        Route::patch('/updatePersonal', ['uses' => 'UsersController@update']);          //It will update details like email,username,password which are present in "USERS" table

        Route::patch('/update', ['uses' => 'AdminsController@update']);

        Route::get('/getAllOfferLetter', ['uses' => 'PlacementOffersController@getAllOfferLetter']);

        Route::post('/{placement_id}/reOpenRegistration', ['uses' => 'PlacementsController@reOpenRegistration']);

    });

});



    Route::group(['prefix'=>'/placement/{placement_id}'],function() {

        Route::get('/', ['uses' => 'PlacementsController@getPlacementPrimary']);            //to find placement basic detial so that id can be known

        Route::get('/selectionRound', ['uses' => 'PlacementsController@showAllSelectionRound']);

        Route::get('/placementDriveDetail', ['uses' => 'PlacementsController@showPlacement']);          //Contains all

        Route::get('/categoryWisePlacementMail', ['uses' => 'PlacementsController@categoryWisePlacementMail']);          //Contains all

    });


    Route::group(['prefix'=>'/categories'],function(){

        Route::get('/', ['uses' => 'CategoryController@index']);

        Route::post('/create', ['uses' => 'CategoryController@create']);

        Route::get('/{id}',[ 'uses' => 'CategoryController@show' ] );

        Route::patch('/{id}',[ 'uses' => 'CategoryController@update' ] );

        Route::delete('/{id}',[ 'uses' => 'CategoryController@destroy' ]);

    });

    Route::group(['prefix'=>'/job_type'],function() {

        Route::get('/', ['uses' => 'JobTypeController@index']);

        Route::post('/create', ['uses' => 'JobTypeController@create']);

        Route::get('/{id}',[ 'uses' => 'JobTypeController@show' ] );

        Route::patch('/{id}',[ 'uses' => 'JobTypeController@update' ] );

        Route::delete('/{id}',[ 'uses' => 'JobTypeController@destroy' ]);


    });

Route::get('/{enroll_no_or_placement_id}/getOfferLetter', ['uses' => 'PlacementOffersController@getOfferLetter']);


Route::get('/placementsAll', ['uses' => 'PlacementsController@placementsAll']);


Route::get('/{user_id}/capablePlacementList', ['uses' => 'StudentsController@capablePlacementList']);



