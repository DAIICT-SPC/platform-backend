<?php


    Route::post('/login', ['uses' => 'AuthController@authenticate']);

    Route::post('/testUser',['uses' => 'UsersController@testUser']);

    Route::get('/', ['uses' => 'AuthController@checkAuthentication'])->middleware('jwt');

//
//Route::middleware('auth:api')->get('/user', function (Request $request) {
//    return $request->user();
//});





    Route::group(["prefix"=>'activation'], function() {

        Route::post('/single', ['uses' => 'ActivationController@createSingleEntry'])->middleware(['jwt','role:admin']);;

        Route::post('/file', ['uses' => 'ActivationController@createViaFile'])->middleware(['jwt','role:admin']);

        Route::get('/activate/{code}' , ['uses' => 'ActivationController@findCode']);

        //Deletion of code is done when user is created hence refer to UsersController@registerUser - users/registerUser

    });

    Route::get('/education/' , ['uses' => 'EducationController@index']);

    Route::group(["prefix" => 'education', 'middleware' => ['jwt']], function() {


            Route::post('/', ['uses' => 'EducationController@createNew']);

            Route::patch('/{id}', ['uses' => 'EducationController@updateEducation']);

            Route::delete('/{id}' , ['uses' => 'EducationController@destroy']);

    });


Route::post('/users/registerUser', ['uses' => 'UsersController@registerUser']);

Route::group(["prefix"=>'users', 'middleware' => ['jwt']], function() {

        Route::get('/', ['uses' => 'UsersController@index']);

        Route::get('/show/{id?}', ['uses' => 'UsersController@show']);

        Route::delete('/{user_id}', ['uses' => 'UsersController@destroy']);


    Route::group(['prefix'=>'{user_id?}/student', 'middleware' => 'role:student'], function(){

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


    Route::group(['prefix'=>'/{user_id?}/company', 'middleware' => 'role:company'],function(){

        Route::patch('/updatePersonal', ['uses' => 'UsersController@update']);          //It will update details like email,username,password which are present in "USERS" table

        Route::patch('/update', ['uses' => 'CompanysController@update']);

        Route::post('/createPlacement', ['uses' => 'PlacementsController@createPlacementDrive']);

        Route::get('/showPlacementSeasonAvailable/', ['uses' => 'PlacementSeasonController@showPlacementSeasonAvailable']);

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

        Route::get('/{placement_id}/showStudentsInRound/{round_no}', ['uses' => 'PlacementsController@showStudentsInRound']);

        Route::post('/{placement_id}/selectStudentsRoundwise', ['uses' => 'PlacementsController@selectStudentsRoundwise']);

        Route::get('/{placement_id}/showPlacementDetails/', ['uses' => 'PlacementsController@showPlacementDetails']);

        Route::patch('/{placement_id}/updateOpenFor/', ['uses' => 'PlacementsController@updateOpenFor']);

        Route::patch('/{placement_id}/updateCriteria/', ['uses' => 'PlacementsController@updateCriteria']);

        Route::patch('/{placement_id}/updatePlacementsPrimary/', ['uses' => 'PlacementsController@updatePlacementsPrimary']);

    });

    Route::group(['prefix'=>'/{user_id}/admin', 'middleware' => 'role:admin'],function(){

        Route::get('/students', ['uses' => 'StudentsController@show']);

        Route::get('/companies', ['uses' => 'CompanysController@show']);

        Route::get('/', ['uses' => 'AdminsController@show']);         //It will get the Company entry from company table

        Route::patch('/updatePersonal', ['uses' => 'UsersController@update']);          //It will update details like email,username,password which are present in "USERS" table

        Route::patch('/update', ['uses' => 'AdminsController@update']);

        Route::get('/getAllOfferLetter', ['uses' => 'PlacementOffersController@getAllOfferLetter']);

        Route::post('/{placement_id}/reOpenRegistration', ['uses' => 'PlacementsController@reOpenRegistration']);

        Route::post('/{placement_id}/selectStudentsRoundwise', ['uses' => 'PlacementsController@selectStudentsRoundwise']);

        Route::patch('/{placement_id}/updateOpenFor/', ['uses' => 'PlacementsController@updateOpenFor']);

        Route::patch('/{placement_id}/updateCriteria/', ['uses' => 'PlacementsController@updateCriteria']);

        Route::patch('/{placement_id}/updatePlacementsPrimary/', ['uses' => 'PlacementsController@updatePlacementsPrimary']);

        Route::get('/{placement_id}/showPlacementDetails/', ['uses' => 'PlacementsController@showPlacementDetails']);

        Route::post('/{placement_id}/selectStudentsFromApplication', ['uses' => 'PlacementsController@selectStudentsFromApplication']);

        Route::post('/{placement_id}/selectStudentsRoundwise', ['uses' => 'PlacementsController@selectStudentsRoundwise']);

        Route::post('/{placement_id}/giveOffer', [ 'uses' => 'PlacementOffersController@giveOfferLetter' ]);

        Route::post('/{placement_id}/cancelOffer', [ 'uses' => 'PlacementOffersController@cancelOfferLetter' ]);

        Route::post('/{placement_id}/openRegistrationForPlacement', ['uses' => 'PlacementsController@openRegistrationForPlacement']);

        Route::post('/{placement_id}/closeRegistrationForPlacement', ['uses' => 'PlacementsController@closeRegistrationForPlacement']);

        Route::patch('/{placement_id}/update/{round_no}', ['uses' => 'PlacementsController@updateDateOfSelectionRound']);

        Route::get('/placement/{placement_id}', ['uses' => 'PlacementApplicationController@showAllApplications']);

        Route::get('/{student_id}/getResume', ['uses' => 'StudentsController@getResume']);

        Route::get('/listOfStudentsPlaced', ['uses' => 'AdminsController@listOfStudentsPlaced']);   // listOfStudentsPlaced?from_date=01-01-2017&to_date=02-02-2017

        Route::get('/listOfStudentsPlacedCategoryWise/{category_id}', ['uses' => 'AdminsController@listOfStudentsPlacedCategoryWise']);    // listOfStudentsPlacedCategoryWise/ 2 ?from_date=2015-01-01&to_date=2017-02-02

        Route::get('/studentsUnplaced', ['uses' => 'AdminsController@studentsUnplaced']);

        Route::get('/studentsUnplacedCategoryWise', ['uses' => 'AdminsController@studentsUnplacedCategoryWise']);

        Route::get('/studentDetail/{enroll_no}', ['uses' => 'AdminsController@studentDetail']);

        Route::get('/placementsCompanyWise/{company_id}', ['uses' => 'AdminsController@placementsCompanyWise']);

        Route::get('/placementDrivesByCompany/{company_id}', ['uses' => 'AdminsController@placementDrivesByCompany']);

        Route::get('/listOfStudentsPlacedInPlacements/{placement_id}', ['uses' => 'AdminsController@listOfStudentsPlacedInPlacements']);

        Route::get('/listOfStudentsRegisteredForPlacement/{placement_id}', ['uses' => 'AdminsController@listOfStudentsRegisteredForPlacement']);

        Route::get('/roundWisePlacementDetail/{placement_id}', ['uses' => 'AdminsController@roundWisePlacementDetail']);

        Route::get('/showPlacementSeasonAvailableToCompany/{company_id}', ['uses' => 'PlacementSeasonController@showPlacementSeasonAvailableToCompany']);

    });

});



    Route::group(['prefix'=>'/placement/{placement_id}' ],function() {

        Route::get('/', ['uses' => 'PlacementsController@getPlacementPrimary']);            //to find placement basic detial so that id can be known

        Route::get('/selectionRound', ['uses' => 'PlacementsController@showAllSelectionRound']);

        Route::get('/placementDriveDetail', ['uses' => 'PlacementsController@showPlacement']);          //Contains all

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

    });

