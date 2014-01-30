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
        Route::get('missingchilds/fields', function(){
            return Response::fields('missingchilds');
        });
        Route::get('article_missingchild/fields', function(){
            return Response::fields('article_missingchild');
        });
        
//        Route::group(array('before' => 'chk_appkey'), function(){     
            Route::post('missingchild/{id}/delete', 'Max\\MissingchildController\\Controllers\\MissingchildController@delete');
            Route::resource('missingchild', 'Max\\Missingchild\\Controllers\\MissingchildController');
            Route::resource('article_missingchild', 'Max\\Missingchild\\Controllers\\ArticleMissingchildController');
//        });   
        
});