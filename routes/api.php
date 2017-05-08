<?php


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

    Route::group(["prefix"=>'users'], function() {

        Route::get('/', ['uses' => 'UsersController@index']);

        Route::post('/registerUser', ['uses' => 'UsersController@registerUser']);

        Route::get('/{id}', ['uses' => 'UsersController@show']);

        Route::delete('/{user_id}', ['uses' => 'UsersController@destroy']);


    Route::group(['prefix'=>'/{user_id}/student'],function(){

        Route::get('/', ['uses' => 'StudentsController@show']);         //It will get the student entry from student table

        Route::patch('/updatePersonal', ['uses' => 'UsersController@update']);      //It will update details like email,username,password which are present in "USERS" table

        Route::patch('/update', ['uses' => 'StudentsController@update']);

        Route::post('/previousEducation', ['uses' => 'StudentsController@storePreviousEducation']);

        Route::get('/previousEducation', ['uses' => 'StudentsController@fetchPreviousEducation']);

        Route::patch('/update/previousEducation/{id}', ['uses' => 'StudentsController@updatePreviousEducation']);

        Route::post('/project', ['uses' => 'StudentsController@storeProjects']);

        Route::get('/project', ['uses' => 'StudentsController@fetchProjects']);

        Route::post('/internship', ['uses' => 'StudentsController@storeInternship']);

        Route::get('/internship', ['uses' => 'StudentsController@fetchInternships']);

    });


    Route::group(['prefix'=>'/{user_id?}/company'],function(){

        Route::get('/', ['uses' => 'CompanysController@show']);         //It will get the Company entry from company table

        Route::patch('/updatePersonal', ['uses' => 'UsersController@update']);          //It will update details like email,username,password which are present in "USERS" table

        Route::patch('/update', ['uses' => 'CompanysController@update']);

    });

    Route::group(['prefix'=>'/{user_id}/admin'],function(){

        Route::get('/', ['uses' => 'AdminsController@show']);         //It will get the Company entry from company table

        Route::patch('/updatePersonal', ['uses' => 'UsersController@update']);          //It will update details like email,username,password which are present in "USERS" table

        Route::patch('/update', ['uses' => 'AdminsController@update']);

    });


});


    Route::group(['prefix'=>'/categories'],function(){

        Route::get('/', ['uses' => 'CategoryController@index']);

        Route::post('/create', ['uses' => 'CategoryController@create']);

        Route::get('/{id}',[ 'uses' => 'CategoryController@show' ] );

        Route::patch('/{id}',[ 'uses' => 'CategoryController@update' ] );

        Route::delete('/{id}',[ 'uses' => 'CategoryController@destroy' ]);

    });


