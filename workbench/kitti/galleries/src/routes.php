<?php
Route::group(array('prefix' => 'api/v1/galleries'), function(){
    Route::get('fields', array('before' => 'chk_appkey2', 'uses' => 'Kitti\\Galleries\\Controllers\\GalleriesController@fields'));
    Route::get('/', array('before' => 'chk_appkey2', 'uses' => 'Kitti\\Galleries\\Controllers\\GalleriesController@lists'));
    Route::get('/{content_type}/{content_id}', array('before' => 'chk_appkey2', 'uses' => 'Kitti\\Galleries\\Controllers\\GalleriesController@content'))->where(array('content_id' => '[0-9]+', 'content_type' => '[a-z]+'));
    Route::get('{id}', array('before' => 'chk_appkey2', 'uses' => 'Kitti\\Galleries\\Controllers\\GalleriesController@listId'))->where('id', '[0-9]+');
    Route::get('{id}/medias', array('before' => 'chk_appkey2', 'uses' => 'Kitti\\Galleries\\Controllers\\GalleriesController@medias'))->where('id', '[0-9]+');
    Route::get('{id}/like', array('before' => 'chk_appkey2', 'uses' => 'Kitti\\Galleries\\Controllers\\GalleriesController@like'))->where('id', '[0-9]+');
    Route::get('{id}/unlike', array('before' => 'chk_appkey2', 'uses' => 'Kitti\\Galleries\\Controllers\\GalleriesController@unlike'))->where('id', '[0-9]+');

    Route::post('/', array('before' => 'chk_appkey2', 'uses' => 'Kitti\\Galleries\\Controllers\\GalleriesController@create'));
    Route::post('{id}/like', array('before' => 'chk_appkey2', 'uses' => 'Kitti\\Galleries\\Controllers\\GalleriesController@like'))->where('id', '[0-9]+');
    Route::post('{id}/unlike', array('before' => 'chk_appkey2', 'uses' => 'Kitti\\Galleries\\Controllers\\GalleriesController@unlike'))->where('id', '[0-9]+');

    Route::put('{id}', array('before' => 'chk_appkey2', 'uses' => 'Kitti\\Galleries\\Controllers\\GalleriesController@update'))->where('id', '[0-9]+');
    Route::delete('{id}', array('before' => 'chk_appkey2', 'uses' => 'Kitti\\Galleries\\Controllers\\GalleriesController@delete'))->where('id', '[0-9]+');
});