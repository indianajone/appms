<?php
Route::group(array('prefix' => 'api/v1/medias'), function(){
    //Route::get('test', 'Kitti\\Medias\\Controllers\\MediasController@test');
    Route::get('fields', array('before' => 'chk_appkey2', 'uses' => 'Kitti\\Medias\\Controllers\\MediasController@fields'));
    // Route::get('/', array('before' => 'chk_appkey2', 'uses' => 'Kitti\\Medias\\Controllers\\MediasController@lists'));
    Route::get('{id}', array('before' => 'chk_appkey2', 'uses' => 'Kitti\\Medias\\Controllers\\MediasController@lists'))->where('id', '[0-9]+');
    //Route::get('/find', array('before' => 'chk_appkey2', 'uses' => 'Kitti\\Medias\\Controllers\\MediasController@find'));
    Route::get('{id}/likes', array('before' => 'chk_appkey2', 'uses' => 'Kitti\\Medias\\Controllers\\MediasController@getLike'))->where('id', '[0-9]+');
    // Route::get('{id}/unlike', array('before' => 'chk_appkey2', 'uses' => 'Kitti\\Medias\\Controllers\\MediasController@unlike'))->where('id', '[0-9]+');

    Route::post('/', array('before' => 'chk_appkey2', 'uses' => 'Kitti\\Medias\\Controllers\\MediasController@create'));
    Route::post('{id}/like', array('before' => 'chk_appkey2', 'uses' => 'Kitti\\Medias\\Controllers\\MediasController@like'))->where('id', '[0-9]+');
    Route::post('{id}/unlike', array('before' => 'chk_appkey2', 'uses' => 'Kitti\\Medias\\Controllers\\MediasController@unlike'))->where('id', '[0-9]+');

    Route::put('{id}', array('before' => 'chk_appkey2', 'uses' => 'Kitti\\Medias\\Controllers\\MediasController@update'))->where('id', '[0-9]+');
    Route::delete('{id}', array('before' => 'chk_appkey2', 'uses' => 'Kitti\\Medias\\Controllers\\MediasController@delete'))->where('id', '[0-9]+');
});