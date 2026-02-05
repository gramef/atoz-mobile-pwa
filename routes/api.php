<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::group([ 'namespace' => 'Api'], function () {
    Route::post('clients', 'NewRequestController@store')->name('new-clients.store');
	
    Route::apiResource( 'languages', 'LanguageController' )->only('index');
    Route::apiResource( 'skills', 'SkillController' )->only('index');

    Route::post( 'organisation', 'OrganisationController@show' )->name('organisation.show');

    Route::post( 'company/{company}', 'CompanyController@show' )->name('companies.show');
    Route::post( 'client/{client}', 'ClientController@show' )->name('clients.show');


    Route::post( 'documents', 'DocumentController@store' )->name('documents.store');
    Route::post( 'documents/delete', 'DocumentController@destroy' )->name('documents.destroy');

    Route::get( 'available-interpreters/{job_type}', 'AgentController@index' )->name('agents.index');
});

Route::group(['namespace' => 'Api\\Mobile'], function () {
    Route::group(['prefix' => 'auth'], function () {
        Route::post('login', 'AuthController@login');
        Route::middleware('auth:api')->group(function () {
            Route::get('me', 'AuthController@me');
        });
    });

    Route::middleware('auth:api')->group(function () {
        Route::get('interpreter-jobs', 'InterpreterJobController@index');
        Route::get('interpreter-jobs/{id}', 'InterpreterJobController@show');
        Route::post('interpreter-jobs/{id}/accept', 'InterpreterJobController@accept');
        Route::post('interpreter-jobs/{id}/complete', 'InterpreterJobController@complete');

        Route::get('translator-jobs', 'TranslatorJobController@index');
        Route::get('translator-jobs/{id}', 'TranslatorJobController@show');
        Route::post('translator-jobs/{id}/accept', 'TranslatorJobController@accept');
    });
});
