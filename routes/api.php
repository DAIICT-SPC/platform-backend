<?php


    Route::post('/login', ['uses' => 'AuthController@authenticate']);

    Route::post('/testUser',['uses' => 'UsersController@testUser']);

    Route::get('/', ['uses' => 'AuthController@checkAuthentication'])->middleware('jwt');

//
//Route::middleware('auth:api')->get('/user', function (Request $request) {
//    return $request->user();
//});

//
//    Route::get('mail/queue',function(){
//
//        Mail::later(5,'vendor.mail.queued_mail',['name' => 'Gaurav'],function ($message){
//            $message->to('foo@example.com','John')->subject('Welcome!');
//        });
//
//        return "email will be sent!";
//
//    });



    Route::group(["prefix"=>'activation'], function() {

        Route::post('/single', ['uses' => 'ActivationController@createSingleEntry'])->middleware(['jwt','role:admin']);;

        Route::post('/file', ['uses' => 'ActivationController@createViaFile'])->middleware(['jwt','role:admin']);

        Route::get('/activate/{code}' , ['uses' => 'ActivationController@findCode']);

        Route::post('/downloadResume', ['uses' => 'StudentsController@downloadResume']);

        //Deletion of code is done when user is created hence refer to UsersController@registerUser - users/registerUser

    });

    Route::get('/education/' , ['uses' => 'EducationController@index']);

    Route::group(["prefix" => 'education', 'middleware' => ['jwt']], function() {


            Route::post('/', ['uses' => 'EducationController@createNew']);

            Route::patch('/{id}', ['uses' => 'EducationController@updateEducation']);

            Route::delete('/{id}' , ['uses' => 'EducationController@destroy']);

    });


    Route::post('/users/registerUser', ['uses' => 'UsersController@registerUser']);



Route::get('/users/{user_id?}/student/dashboard', ['uses' => 'StudentsController@dashboard']);


Route::group(["prefix"=>'users', 'middleware' => ['jwt']], function() {

        Route::get('/', ['uses' => 'UsersController@index']);

        Route::get('/show/{id?}', ['uses' => 'UsersController@show']);

        Route::delete('/{user_id}', ['uses' => 'UsersController@destroy']);

        Route::post('/storeProfilePicture/{user_id}', ['uses' => 'UsersController@storeProfilePicture']);

        Route::get('/viewProfilePicture/{user_id}', ['uses' => 'UsersController@viewProfilePicture']);

        Route::post('/removeProfilePicture/{user_id}', ['uses' => 'UsersController@removeProfilePicture']);


    Route::group(['prefix'=>'{user_id?}/student', 'middleware' => 'role:student'], function(){

            Route::get('/show', ['uses' => 'StudentsController@show']);

            Route::get('/jobProfile', ['uses' => 'PlacementsController@jobProfile']);

            Route::patch('/updatePersonal', ['uses' => 'UsersController@update']);      //It will update details like email,username,password which are present in "USERS" table

            Route::patch('/update', ['uses' => 'StudentsController@update']);

        //    Route::post('/previousEducation', ['uses' => 'StudentsController@storePreviousEducation']);

        //    Route::get('/previousEducation', ['uses' => 'StudentsController@fetchPreviousEducation']);

        //    Route::patch('/update/previousEducation/{id}', ['uses' => 'StudentsController@updateEducation']);

            Route::post('/project', ['uses' => 'StudentsController@storeProjects']);

            Route::get('/project', ['uses' => 'StudentsController@fetchProjects']);

            Route::post('/internship', ['uses' => 'StudentsController@storeInternship']);

            Route::get('/internship', ['uses' => 'StudentsController@fetchInternships']);

            Route::post('/placementRegistration', ['uses' => 'PlacementApplicationController@studentRegistration']);

            Route::post('/cancelRegistration', ['uses' => 'PlacementApplicationController@cancelRegistration']);

            Route::post('/education', ['uses' => 'StudentsController@storeStudentEducation']);

            Route::get('/education', ['uses' => 'StudentsController@fetchEducation']);

            Route::patch('/education/{education_id}', ['uses' => 'StudentsController@updateEducation']);

            Route::post('/uploadResume', ['uses' => 'StudentsController@uploadResume']);

            Route::get('/getResume', ['uses' => 'StudentsController@getResume']);

            Route::post('/eligibility', ['uses' => 'StudentsController@eligibility']);

            Route::get('/{placement_id}/showPlacementDetails/', ['uses' => 'PlacementsController@showPlacementDetails']);

            Route::get('/applyToAppliedButton/{placement_id}', ['uses' => 'PlacementApplicationController@applyToAppliedButton']);

            Route::get('/fetchEducationAccordingToCategoryForStudent', ['uses' => 'CategoryController@fetchEducationAccordingToCategoryForStudent']);    //for entries combo box in placement criteria page

    });


    Route::group(['prefix'=>'/{user_id?}/company', 'middleware' => 'role:company'],function(){

        Route::get('/show', ['uses' => 'CompanysController@show']);

        Route::patch('/updatePersonal', ['uses' => 'UsersController@update']);          //It will update details like email,username,password which are present in "USERS" table

        Route::patch('/update', ['uses' => 'CompanysController@update']);

        Route::post('/createPlacement', ['uses' => 'PlacementsController@createPlacementDrive']);

        Route::get('/showPlacementSeasonAvailable/', ['uses' => 'PlacementSeasonController@showPlacementSeasonAvailable']);

        Route::post('/{placement_id}/setSelectionRound', ['uses' => 'PlacementsController@selectionRound']);

        Route::get('/{placement_id}/showOpenFor', ['uses' => 'PlacementsController@showOpenForCategories']);    //for entries combo box in placement criteria page

        Route::get('/fetchEducationAccordingToCategory/{category_id}', ['uses' => 'CategoryController@fetchEducationAccordingToCategory']);    //for entries combo box in placement criteria page

        Route::post('/{placement_id}/setPlacementCriteria', ['uses' => 'PlacementsController@setPlacementCriteria']);

        Route::get('/placementApplications/{placement_id}', ['uses' => 'PlacementApplicationController@showAllApplications']);

        Route::post('/{placement_id}/setOpenForDetails', ['uses' => 'PlacementsController@placementDriveOpenFor']);

        Route::post('/{placement_id}/openRegistrationForPlacement', ['uses' => 'PlacementsController@openRegistrationForPlacement']);

        Route::post('/{placement_id}/closeRegistrationForPlacement', ['uses' => 'PlacementsController@closeRegistrationForPlacement']);

        Route::patch('/{placement_id}/update/{round_no}', ['uses' => 'PlacementsController@updateDateOfSelectionRound']);

        Route::get('/{student_id}/getResume', ['uses' => 'StudentsController@getResume']);

        Route::post('/{placement_id}/giveOffer', [ 'uses' => 'PlacementOffersController@giveOfferLetter' ]);

        Route::post('/{placement_id}/cancelOffer', [ 'uses' => 'PlacementOffersController@cancelOfferLetter' ]);

        Route::post('/{placement_id}/reOpenRegistration', ['uses' => 'PlacementsController@reOpenRegistration']);

        Route::post('/{placement_id}/selectStudentsFromApplication', ['uses' => 'PlacementsController@selectStudentsFromApplication']);

        Route::get('/{placement_id}/showStudentsInRound/{round_no}', ['uses' => 'PlacementsController@showStudentsInRound']);

        Route::post('/{placement_id}/selectStudentsRoundwise', ['uses' => 'PlacementsController@selectStudentsRoundwise']);

        Route::post('/{placement_id}/selectStudentsFromLastRound', ['uses' => 'PlacementsController@selectStudentsFromLastRound']);

        Route::get('/{placement_id}/showPlacementDetails/', ['uses' => 'PlacementsController@showPlacementDetails']);

        Route::patch('/{placement_id}/updateOpenFor/', ['uses' => 'PlacementsController@updateOpenFor']);

        Route::patch('/{placement_id}/updateCriteria/', ['uses' => 'PlacementsController@updateCriteria']);

        Route::patch('/{placement_id}/updatePlacementsPrimary/', ['uses' => 'PlacementsController@updatePlacementsPrimary']);

        Route::get('/remainingStudentsInApplication/{placement_id}', ['uses' => 'PlacementsController@remainingStudentsInApplication']);

        Route::get('/remainingStudentsRoundwise/{placement_id}/{round_no}', ['uses' => 'PlacementsController@remainingStudentsRoundwise']);

        Route::get('/checkIfRoundsCompleted/{placement_id}/{round_no}', ['uses' => 'PlacementsController@checkIfRoundsCompleted']);

        Route::get('/remainingStudentsForOffer/{placement_id}', ['uses' => 'PlacementsController@remainingStudentsForOffer']);

        Route::get('/placementPrimaryAll', ['uses' => 'PlacementsController@placementPrimaryAll']);          //Contains all

        Route::post('/downloadResume', ['uses' => 'StudentsController@downloadResume']);

    });

    Route::group(['prefix'=>'/{user_id}/admin', 'middleware' => 'role:admin'],function(){

        Route::get('/students', ['uses' => 'StudentsController@index']);

        Route::get('/', ['uses' => 'AdminsController@index']);

        Route::get('/
        ', ['uses' => 'CompanysController@index']);

        Route::get('/show', ['uses' => 'AdminsController@show']);

        Route::patch('/updatePersonal', ['uses' => 'UsersController@update']);          //It will update details like email,username,password which are present in "USERS" table

        Route::patch('/update', ['uses' => 'AdminsController@update']);

        Route::get('/getAllOfferLetter/{placement_id}', ['uses' => 'PlacementOffersController@getAllOfferLetter']);

        Route::post('/{placement_id}/reOpenRegistration', ['uses' => 'PlacementsController@reOpenRegistration']);

        Route::post('/{placement_id}/selectStudentsRoundwise', ['uses' => 'PlacementsController@selectStudentsRoundwise']);

        Route::patch('/{placement_id}/updateOpenFor/', ['uses' => 'PlacementsController@updateOpenFor']);

        Route::patch('/{placement_id}/updateCriteria/', ['uses' => 'PlacementsController@updateCriteria']);

        Route::patch('/{placement_id}/updatePlacementsPrimary/', ['uses' => 'PlacementsController@updatePlacementsPrimary']);

        Route::get('/{placement_id}/showPlacementDetails/', ['uses' => 'PlacementsController@showPlacementDetails']);

        Route::post('/{placement_id}/selectStudentsFromApplication', ['uses' => 'PlacementsController@selectStudentsFromApplication']);

        Route::post('/{placement_id}/selectStudentsRoundwise', ['uses' => 'PlacementsController@selectStudentsRoundwise']);

        Route::post('/{placement_id}/selectStudentsFromLastRound', ['uses' => 'PlacementsController@selectStudentsFromLastRound']);

        Route::post('/{placement_id}/giveOffer', [ 'uses' => 'PlacementOffersController@giveOfferLetter' ]);

        Route::post('/{placement_id}/cancelOffer', [ 'uses' => 'PlacementOffersController@cancelOfferLetter' ]);

        Route::post('/{placement_id}/openRegistrationForPlacement', ['uses' => 'PlacementsController@openRegistrationForPlacement']);

        Route::post('/{placement_id}/closeRegistrationForPlacement', ['uses' => 'PlacementsController@closeRegistrationForPlacement']);

        Route::patch('/{placement_id}/update/{round_no}', ['uses' => 'PlacementsController@updateDateOfSelectionRound']);

        Route::get('/placementApplications/{placement_id}', ['uses' => 'PlacementApplicationController@showAllApplications']);

        Route::get('/getResume', ['uses' => 'StudentsController@getResume']);

        Route::get('/listOfStudentsPlaced/{placement_season_id}', ['uses' => 'AdminsController@listOfStudentsPlaced']);   // listOfStudentsPlaced?from_date=01-01-2017&to_date=02-02-2017

        Route::get('/listOfStudentsPlacedCategoryWise/{placement_season_id}/{category_id}', ['uses' => 'AdminsController@listOfStudentsPlacedCategoryWise']);    // listOfStudentsPlacedCategoryWise/ 2 ?from_date=2015-01-01&to_date=2017-02-02

        Route::get('/studentsUnplaced/{placement_season_id}', ['uses' => 'AdminsController@studentsUnplaced']);

        Route::get('/studentsUnplacedCategoryWise', ['uses' => 'AdminsController@studentsUnplacedCategoryWise']);

        Route::get('/studentDetail/{enroll_no}', ['uses' => 'AdminsController@studentDetail']);

        Route::get('/placementDrivesByCompany/{company_id}', ['uses' => 'AdminsController@placementDrivesByCompany']);

        Route::get('/listOfStudentsPlacedInPlacements/{placement_id}', ['uses' => 'AdminsController@listOfStudentsPlacedInPlacements']);

        Route::get('/listOfStudentsRegisteredForPlacement/{placement_id}', ['uses' => 'AdminsController@listOfStudentsRegisteredForPlacement']);

        Route::get('/roundWisePlacementDetail/{placement_id}/{round_id}', ['uses' => 'AdminsController@roundWisePlacementDetail']);

        Route::get('/showPlacementSeasonAvailableToCompany/{company_id}', ['uses' => 'PlacementSeasonController@showPlacementSeasonAvailableToCompany']);

        Route::post('/externalAllowToStudents/{placement_id}', ['uses' => 'AdminsController@externalAllowToStudents']);

        Route::get('/remainingStudentsInApplication/{placement_id}', ['uses' => 'PlacementsController@remainingStudentsInApplication']);

        Route::get('/remainingStudentsRoundwise/{placement_id}/{round_no}', ['uses' => 'PlacementsController@remainingStudentsRoundwise']);

        Route::get('/remainingStudentsForOffer/{placement_id}', ['uses' => 'PlacementsController@remainingStudentsForOffer']);

        Route::post('/downloadResume', ['uses' => 'StudentsController@downloadResume']);

    });

});



    Route::group(['prefix'=>'/placement/{placement_id}' ],function() {

        Route::get('/', ['uses' => 'PlacementsController@getPlacementPrimary']);            //to find placement basic detial so that id can be known

        Route::get('/selectionRound', ['uses' => 'PlacementsController@showAllSelectionRound']);

        Route::get('/categoryWisePlacementMail', ['uses' => 'PlacementsController@categoryWisePlacementMail']);          //Contains all

    });

    Route::get('/categories/', ['uses' => 'CategoryController@index']);

    Route::group(['prefix'=>'/categories', 'middleware' => ['jwt', 'role:admin']],function(){


        Route::post('/create', ['uses' => 'CategoryController@create']);

        Route::get('/{id}',[ 'uses' => 'CategoryController@show' ] );

        Route::patch('/{id}',[ 'uses' => 'CategoryController@update' ] );

        Route::delete('/{id}',[ 'uses' => 'CategoryController@destroy' ]);

    });


    Route::get('/job_type/', ['uses' => 'JobTypeController@index']);


    Route::group(['prefix'=>'/job_type', 'middleware' => ['jwt'], 'role:admin'],function() {

            Route::post('/create', ['uses' => 'JobTypeController@create']);

            Route::get('/{id}',[ 'uses' => 'JobTypeController@show' ] );

            Route::patch('/{id}',[ 'uses' => 'JobTypeController@update' ] );

            Route::delete('/{id}',[ 'uses' => 'JobTypeController@destroy' ]);


    });


Route::get('/{enroll_no_or_placement_id}/getOfferLetter', ['uses' => 'PlacementOffersController@getOfferLetter']);


Route::get('/placementsAll', ['uses' => 'PlacementsController@placementsAll']);





    Route::group(["prefix" => 'placement_season', 'middleware' => ['jwt'], 'role:admin'], function() {

        Route::get('/' , ['uses' => 'PlacementSeasonController@index']);

        Route::get('/show/{placement_season_id}' , ['uses' => 'PlacementSeasonController@show']);

        Route::post('/', ['uses' => 'PlacementSeasonController@create']);

        Route::patch('/{placement_season_id}', ['uses' => 'PlacementSeasonController@update']);

        Route::delete('/{placement_season_id}' , ['uses' => 'PlacementSeasonController@destroy']);

        Route::post('/startSeason/{placement_season_id}', ['uses' => 'PlacementSeasonController@startSeason']);

        Route::post('/closeSeason/{placement_season_id}', ['uses' => 'PlacementSeasonController@closeSeason']);

        Route::post('/allowCompany/{placement_season_id}', ['uses' => 'PlacementSeasonController@allowCompany']);

        Route::post('/allowCompanies/{placement_season_id}', ['uses' => 'PlacementSeasonController@allowCompanies']);

        Route::post('/disallowCompany/{placement_season_id}', ['uses' => 'PlacementSeasonController@disallowCompany']);

        Route::get('/allAllowedCompanies/{placement_season_id}', ['uses' => 'PlacementSeasonController@allAllowedCompanies']);

        Route::get('/remainingCompanies/{placement_season_id}', ['uses' => 'PlacementSeasonController@remainingCompanies']);

        Route::get('/placementsInPlacementSeason/{placement_season_id}', ['uses' => 'PlacementSeasonController@placementsInPlacementSeason']);

        Route::get('/placementsCompanyWiseListing/{placement_season_id}/{company_id}', ['uses' => 'PlacementSeasonController@placementsCompanyWiseListing']);

        Route::get('/companiesAllowedOrNot/{placement_season_id}', ['uses' => 'PlacementSeasonController@companiesAllowedOrNot']);

    });


