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
Route::group(array('prefix' => 'v1', 'before'=>'auth'), function()
{
    Route::any('users/login', 'Max\\User\\Controllers\\UserController@doLogin');
    Route::any('users/logout', 'Max\\User\\Controllers\\UserController@doLogout');
    Route::resource('users', 'Max\\User\\Controllers\\UserController',
        array('only' => array('index', 'create', 'edit'))
    );
});



Route::group(array('prefix' => 'api/v1'), function()
{
    // Auth
    Route::any('users/login', 'Max\\User\\Controllers\\ApiUserController@doLogin');
    Route::any('users/logout', 'Max\\User\\Controllers\\ApiUserController@doLogout');
    
    // Users
    Route::get('users/fields', function(){
        return Response::fields('users');
    });
    Route::any('users/{id}/delete', 'Max\\User\\Controllers\\ApiUserController@delete');
    Route::get('users/{id}/resetPassword', 'Max\\User\\Controllers\\ApiUserController@resetPassword'); 
    Route::any('users/{id}/roles/{action}', 'Max\\User\\Controllers\\ApiUserController@manageRole'); 
    Route::resource('users', 'Max\\User\\Controllers\\ApiUserController');
        
});