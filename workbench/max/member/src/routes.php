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
    Route::any('members/login', 'Max\\Member\\Controllers\\MemberController@doLogin');
    Route::any('members/logout', 'Max\\Member\\Controllers\\MemberController@doLogout');

    // Member
	Route::get('members/fields', function(){
            return Response::fields('members');
        });
        
	Route::post('members/{id}/delete', 'Max\\Member\\Controllers\\MemberController@delete');
	Route::get('members/{id}/resetPassword', 'Max\\Member\\Controllers\\MemberController@resetPassword'); 
	Route::resource('members', 'Max\\Member\\Controllers\\MemberController');
        
        Route::get('members/{id}/otp', 'Max\\Member\\Controllers\\MemberController@requestOTP');
        Route::post('members/{id}/otp/{otp}', 'Max\\Member\\Controllers\\MemberController@requestOTP');
});