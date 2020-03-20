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
Route::get('/projects/code/','ProjectController@code');
//Job
Route::get('/jobs','JobController@index');
Route::post('/jobs/store','JobController@store');
Route::patch('/jobs/update/{id}', 'JobController@update');
Route::delete('/jobs/delete/{id}','JobController@destroy');
Route::get('/jobs/show/{id}', 'JobController@show');
Route::get('/jobs/code/','JobController@code');
//Material
Route::get('/materials','MaterialsController@index');
Route::post('/materials/store','MaterialsController@store');
Route::patch('/materials/update/{id}', 'MaterialsController@update');
Route::delete('/materials/delete/{id}','MaterialsController@destroy');
Route::get('/materials/show/{id}', 'MaterialsController@show');
Route::get('/materials/code/','MaterialsController@code');
//AHS 
Route::get('/ahs','AHSController@index');
Route::get('/ahs/index','AHSController@all');
Route::post('/ahs/store','AHSController@store');
Route::patch('/ahs/update/{id}','AHSController@update');
Route::delete('/ahs/delete/{id}','AHSController@destroy');
Route::get('/ahs/show/{id}', 'AHSController@showByID');
Route::post('/ahs/copy','AHSController@copy_ahs');
Route::get('/ahs/code/','AHSController@code');
Route::get('/ahs/show','AHSController@show_detailsNotNull');
//AHS Details
Route::get('/ahs_details','AHSDetailsController@index');
Route::patch('/ahs_details/update/{id}','AHSDetailsController@update');
Route::delete('/ahs_details/delete/{id}', 'AHSDetailsController@destroy');
Route::get('/ahs_details/show/{id}', 'AHSDetailsController@showbyID');
//RAB
Route::get('/rabs','RABController@index');
Route::get('/rabs/index','RABController@all');
Route::post('/rabs/store','RABController@store');
Route::post('/rabs/storeN','RABController@storeN');
Route::patch('/rabs/update/{id}','RABController@update');
Route::delete('/rabs/delete/{id}','RABController@destroy');
Route::get('/rabs/code/','RABController@code');
//RAB Details
Route::get('/rab_details','RABDetailsController@index');
Route::patch('/rab_details/update/{id}','RABDetailsController@update');
Route::delete('/rab_details/delete/{id}','RABDetailsController@destroy');
Route::get('rab_details/show/{id}','RABDetailsController@show');
//Store
Route::get('/stores','StoreController@index');
Route::post('/stores/store', 'StoreController@store');
Route::patch('/stores/update/{id}', 'StoreController@update');
Route::delete('/stores/delete/{id}', 'StoreController@destroy');
Route::get('/stores/show/{id}', 'StoreController@showByID');
Route::get('/stores/code/','StoreController@code');
//Satuan
Route::get('/satuans','SatuanController@index');
Route::post('/satuans/store', 'SatuanController@store');
Route::patch('/satuans/update/{id}','SatuanController@update');
Route::delete('/satuans/delete/{id}','SatuanController@destroy');
//Structure
Route::get('/structure','StructureController@index');
Route::post('/structure/store', 'StructureController@store');
Route::patch('/structure/update/{id}','StructureController@update');
Route::delete('/structure/delete/{id}','StructureController@destroy');
//Structure Details
Route::get('/structure_details','StructureDetailsController@index');
Route::delete('/structure_details/delete/{id}','StructureDetailsController@destroy');
Route::get('structure_details/show/{id}','StructureDetailsController@show');
//Group
Route::get('/groups','GroupController@index');
Route::post('/groups/store','GroupController@store');
Route::patch('/groups/update/{id}','GroupController@update');
Route::delete('/groups/delete/{id}','GroupController@destroy');
//Group Details
Route::get('/group_details','GroupDetailsController@index');
Route::delete('/group_details/delete/{id}','GroupDetailsController@destroy');
Route::get('group_details/show/{id}','GroupDetailsController@show');
//Task Sub
Route::get('/task_sub','TaskSubController@index');
Route::post('/task_sub/store', 'TaskSubController@store');
Route::patch('/task_sub/update/{id}','TaskSubController@update');
Route::delete('/task_sub/delete/{id}','TaskSubController@destroy');
//Task Sub Details
Route::get('/tasksub_Sdetails','TaskSubDetailsController@index');
Route::delete('/tasksub_details/delete/{id}','TaskSubDetailsController@destroy');
Route::get('tasksub_details/show/{id}','TaskSubDetailsController@show');
//AHS Lokal Details
Route::get('/ahs_lokal_details','AHSLokalDetailsController@index');
Route::patch('/ahs_lokal_details/update/{id}','AHSLokalDetailsController@update');
Route::delete('/ahs_lokal_details/delete/{id}', 'AHSLokalDetailsController@destroy');
Route::get('/ahs_lokal_details/show/{id}', 'AHSLokalDetailsController@showByID');
//Reports
Route::get('/ahs_master_report/{id}','ReportsController@analisa_task');
Route::get('/ahs_lokal_report/{id}','ReportsController@analisa_lokal');
Route::get('/rab_report/{id}','ReportsController@rab');
Route::get('/rab_bq_report/{id}','ReportsController@rab_bq');
Route::get('/analisa_task_all','ReportsController@analisa_task_all');