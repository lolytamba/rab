<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

//User
Route::group([
    'prefix' => 'auth'
], function () {
    Route::post('/login','UserController@login');
    Route::post('/register','UserController@create');

    Route::group([
      'middleware' => 'auth:api'
    ], function() {
        Route::post('/logout', 'UserController@logout');
        Route::get('/user', 'UserController@user');
    });
});
//Project
Route::get('/projects','ProjectController@index');
Route::post('/projects/store', 'ProjectController@store');
Route::patch('/projects/update/{id}', 'ProjectController@update');
Route::delete('/projects/delete/{id}', 'ProjectController@destroy');
Route::get('/projects/show/{id}', 'ProjectController@showByID');
Route::get('/count','ProjectController@count');
//Job
Route::get('/jobs','JobController@index');
Route::post('/jobs/store','JobController@store');
Route::patch('/jobs/update/{id}', 'JobController@update');
Route::delete('/jobs/delete/{id}','JobController@destroy');
Route::get('/jobs/show/{id}', 'JobController@show');
//Material
Route::get('/materials','MaterialsController@index');
Route::post('/materials/store','MaterialsController@store');
Route::patch('/materials/update/{id}', 'MaterialsController@update');
Route::delete('/materials/delete/{id}','MaterialsController@destroy');
Route::get('/materials/show/{id}', 'MaterialsController@show');
//AHS 
Route::get('/ahs','AHSController@index');
Route::post('/ahs/store','AHSController@store');
Route::patch('/ahs/update/{id}','AHSController@update');
Route::delete('/ahs/delete/{id}','AHSController@destroy');
Route::get('/ahs/show/{id}', 'AHSController@showByID');
//AHS Details
Route::get('/ahs_details','AHSDetailsController@index');
Route::patch('/ahs_details/update/{id}','AHSDetailsController@update');
Route::delete('/ahs_details/delete/{id}', 'AHSDetailsController@destroy');
Route::get('/ahs_details/show/{id}', 'AHSDetailsController@showbyID');
//Material Details
Route::get('/material_details','MaterialDetailsController@index');
Route::post('/material_details/store','MaterialDetailsController@store');
//RAB
Route::get('/rabs','RABController@index');
Route::post('/rabs/store','RABController@store');
Route::post('/rabs/storeN','RABController@storeN');
Route::patch('/rabs/update/{id}','RABController@update');
Route::delete('/rabs/delete/{id}','RABController@destroy');
Route::get('/rabs/total','RABController@total');
Route::get('/count_rab','RABController@count');
Route::post('/rabs/storeN','RABController@storeN');
//RAB Details
Route::get('/rab_details','RABDetailsController@index');
Route::patch('/rab_details/update/{id}','RABDetailsController@update');
Route::delete('/rab_details/delete/{id}','RABDetailsController@destroy');
//Store
Route::get('/stores','StoreController@index');
Route::post('/stores/store', 'StoreController@store');
Route::patch('/stores/update/{id}', 'StoreController@update');
Route::delete('/stores/delete/{id}', 'StoreController@destroy');
Route::get('/stores/show/{id}', 'StoreController@showByID');
Route::get('/count_store', 'StoreController@count');
//Satuan
Route::get('/satuans','SatuanController@index');
Route::post('/satuans/store', 'SatuanController@store');
//Structure
Route::get('/structure','StructureController@index');
Route::post('/structure/store', 'StructureController@store');
Route::patch('/structure/update/{id}','StructureController@update');
Route::delete('/structure/delete/{id}','StructureController@destroy');
//Group
Route::get('/groups','GroupController@index');
Route::post('/groups/store','GroupController@store');
Route::patch('/groups/update/{id}','GroupController@update');
Route::delete('/groups/delete/{id}','GroupController@destroy');
//Task Sub
Route::get('/task_sub','TaskSubController@index');
Route::post('/task_sub/store', 'TaskSubController@store');
Route::patch('/task_sub/update/{id}','TaskSubController@update');
Route::delete('/task_sub/delete/{id}','TaskSubController@destroy');
//Reports
Route::get('/analisa_task/{id}','ReportsController@analisa_task');
Route::get('/analisa_task_all','ReportsController@analisa_task_all');