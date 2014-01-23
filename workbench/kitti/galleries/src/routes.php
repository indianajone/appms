<?php
Route::group(array('prefix' => 'api/v1/galleries'), function(){
    Route::get('fields', array('before' => 'chk_appkey2', 'uses' => 'Kitti\\Galleries\\Controllers\\GalleriesController@fields'));
    Route::get('/', array('before' => 'chk_appkey2', 'uses' => 'Kitti\\Galleries\\Controllers\\GalleriesController@lists'));
    Route::get('{id}', array('before' => 'chk_appkey2', 'uses' => 'Kitti\\Galleries\\Controllers\\GalleriesController@listsId'))->where('id', '[0-9]+');
    //Route::get('/find', array('before' => 'chk_appkey2', 'uses' => 'Kitti\\Galleries\\Controllers\\GalleriesController@find'));
    Route::get('{id}/like', array('before' => 'chk_appkey2', 'uses' => 'Kitti\\Galleries\\Controllers\\GalleriesController@like'))->where('id', '[0-9]+');
    Route::get('{id}/unlike', array('before' => 'chk_appkey2', 'uses' => 'Kitti\\Galleries\\Controllers\\GalleriesController@unlike'))->where('id', '[0-9]+');

    Route::post('/', array('before' => 'chk_appkey2', 'uses' => 'Kitti\\Galleries\\Controllers\\GalleriesController@create'));
    Route::post('{id}/like', array('before' => 'chk_appkey2', 'uses' => 'Kitti\\Galleries\\Controllers\\GalleriesController@like'))->where('id', '[0-9]+');
    Route::post('{id}/unlike', array('before' => 'chk_appkey2', 'uses' => 'Kitti\\Galleries\\Controllers\\GalleriesController@unlike'))->where('id', '[0-9]+');

    Route::put('{id}', array('before' => 'chk_appkey2', 'uses' => 'Kitti\\Galleries\\Controllers\\GalleriesController@update'))->where('id', '[0-9]+');
    Route::delete('{id}', array('before' => 'chk_appkey2', 'uses' => 'Kitti\\Galleries\\Controllers\\GalleriesController@delete'))->where('id', '[0-9]+');
});