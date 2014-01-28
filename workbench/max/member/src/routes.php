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
	Route::get('members/fields', function(){
        return Response::fields('members');
    });
	Route::post('members/{id}/delete', 'Max\\Member\\Controllers\\MemberController@delete');
	Route::resource('members', 'Max\\Member\\Controllers\\MemberController');
});