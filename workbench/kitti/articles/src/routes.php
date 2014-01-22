<?php
use Kitti\Articles\Controllers\ArticleController;


Route::filter('chk_appkey2', function()
{
    // $appkey = Input::get('appkey');
    
    // $ak = DB::table('applications')->select('appkey')->get();            
    // foreach ($ak as $aks) {
    //     if($aks->appkey == $appkey){
    //         break;
    //     }else{
    //         $code = 204; $msg = 'Appkey is not valid.';
    //         $arrResp['header']['code'] = $code;
    //         $arrResp['header']['message'] = $msg;

    //         $response = Response::json($arrResp);
    //         return $response;
    //     }
    // }
});

Route::group(array('prefix' => 'api/v1/articles'), function(){
    Route::get('fields', array('before' => 'chk_appkey2', 'uses' => 'Kitti\\Articles\\Controllers\\ArticleController@fields'));
    Route::get('/', array('before' => 'chk_appkey2', 'uses' => 'Kitti\\Articles\\Controllers\\ArticleController@lists'));
    Route::get('{id}', array('before' => 'chk_appkey2', 'uses' => 'Kitti\\Articles\\Controllers\\ArticleController@listsId'))->where('id', '[0-9]+');
    Route::get('/find', array('before' => 'chk_appkey2', 'uses' => 'Kitti\\Articles\\Controllers\\ArticleController@find'));
    Route::get('{id}/like', array('before' => 'chk_appkey2', 'uses' => 'Kitti\\Articles\\Controllers\\ArticleController@like'))->where('id', '[0-9]+');
    Route::get('{id}/unlike', array('before' => 'chk_appkey2', 'uses' => 'Kitti\\Articles\\Controllers\\ArticleController@unlike'))->where('id', '[0-9]+');

    Route::post('/', array('before' => 'chk_appkey2', 'uses' => 'Kitti\\Articles\\Controllers\\ArticleController@create'));
    Route::post('{id}/like', array('before' => 'chk_appkey2', 'uses' => 'Kitti\\Articles\\Controllers\\ArticleController@like'))->where('id', '[0-9]+');
    Route::post('{id}/unlike', array('before' => 'chk_appkey2', 'uses' => 'Kitti\\Articles\\Controllers\\ArticleController@unlike'))->where('id', '[0-9]+');

    Route::put('{id}', array('before' => 'chk_appkey2', 'uses' => 'Kitti\\Articles\\Controllers\\ArticleController@update'))->where('id', '[0-9]+');
    Route::delete('{id}', array('before' => 'chk_appkey2', 'uses' => 'Kitti\\Articles\\Controllers\\ArticleController@delete'))->where('id', '[0-9]+');
});