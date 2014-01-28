<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

Route::group(array('prefix' => 'api/v1'), function()
{
    // Auth
    Route::any('users/login', 'Max\\User\\Controllers\\UserController@doLogin');
    Route::any('users/logout', 'Max\\User\\Controllers\\UserController@doLogout');
    
    // Users
    Route::get('users/fields', function(){
        return Response::fields('users');
    });
    Route::post('users/{id}/delete', 'Max\\User\\Controllers\\UserController@delete');
    Route::get('users/{id}/resetPassword', 'Max\\User\\Controllers\\UserController@resetPassword'); 
    Route::post('users/{id}/roles/{action}', 'Max\\User\\Controllers\\UserController@manageRole'); 
    Route::resource('users', 'Max\\User\\Controllers\\UserController');
        
});