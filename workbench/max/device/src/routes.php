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

Route::filter('chk_appkey', function()
{
    $appkey = Input::get('appkey', null);
    
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
//        Route::group(array('before' => 'chk_appkey'), function(){
//            Route::get('member/{id}/devices', 'Max\\Device\\Controllers\\DeviceController@store');
//            Route::get('member/{id}/devices', 'Max\\Device\\Controllers\\DeviceController@update');
//            Route::get('member/{id}/devices', 'Max\\Device\\Controllers\\DeviceController@destroy');
            
            Route::post('member/{id}/devices', 'Max\\Device\\Controllers\\DeviceController@store');
            Route::put('member/{id}/devices', 'Max\\Device\\Controllers\\DeviceController@update');
            Route::delete('member/{id}/devices', 'Max\\Device\\Controllers\\DeviceController@destroy');
            
//            Route::resource('member/{id}/devices', 'Max\\Device\\Controllers\\DeviceController');
//        });
        
});