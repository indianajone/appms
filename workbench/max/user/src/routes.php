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

<<<<<<< HEAD
Route::filter('chk_appkey', function()
{
    $appkey = Input::get('appkey');
    
    $ak = DB::table('applications')->select('appkey')->get();            
    foreach ($ak as $aks) {
        if($aks->appkey == $appkey){
            break;
        }else{
            $code = 204; $msg = 'Appkey is not valid.';
            $arrResp['header']['code'] = $code;
            $arrResp['header']['message'] = $msg;

            $response = Response::json($arrResp);
            return $response;
        }
    }
});

Route::group(array('prefix' => 'api/v1'), function()
{
	Route::get('/', function()
	{
	    return 'User Package';
	});
        
        Route::any('auth/login', 'Max\\User\\Controllers\\UserController@doLogin');
        Route::any('auth/logout', 'Max\\User\\Controllers\\UserController@doLogout');
        
        Route::get('users/hasParent', 'Max\\User\\Controllers\\UserController@hasParent');
        Route::get('users/resetPassword', 'Max\\User\\Controllers\\UserController@resetPassword');
        
        Route::get('users/fields', 'Max\\User\\Controllers\\UserController@fields');
        
//        Route::get('members/fields/{table}/{format?}', 'MemberController@fields');
//        Route::get('members/login', 'MemberController@doLogin');        
        
        // Route::group(array('before' => 'chk_appkey'), function(){
//            Route::get('members/{id}/devices', 'MemberController@registerDevice');
            Route::any('users/{id}/attach', 'Max\\User\\Controllers\\UserController@attachRole');
            Route::resource('users', 'Max\\User\\Controllers\\UserController');
//            Route::resource('members', 'MemberController');
//            Route::resource('member/{id}/devices', 'DeviceController');
        // });   
=======
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
    Route::any('users/{id}/roles/{action}', 'Max\\User\\Controllers\\UserController@manageRole'); 
    Route::resource('users', 'Max\\User\\Controllers\\UserController');
>>>>>>> best
        
});