<?php
Route::group(array('prefix' => 'galleries'), function(){
    Route::get('fields', 'GalleriesController@fields');
    Route::get('/', 'GalleriesController@lists');
    Route::get('{id}/like', 'GalleriesController@getLike')->where('id', '[0-9]+');
//    Route::get('{id}/medias', 'GalleriesController@medias')->where('id', '[0-9]+');
//    Route::get('{content_type}/{content_id}', 'GalleriesController@unlike');
    // curl -X POST http://localhost/dev/appms/public/article -d appkey=appkey -d p=p
    // http://localhost/dev/appms/public/articles?appkey=1&user_id=1&title=1&content=1&gallery_id=10&pre_title=1&picture=1&teaser=1&wrote_by=1&tags=1&categories=1,2,3,4,5
    // ADD
    Route::post('/', 'GalleriesController@postCreate');
    // Update
    Route::put('{id}', 'GalleriesController@update')->where('id', '[0-9]+');
    Route::put('{id}/like', 'GalleriesController@like')->where('id', '[0-9]+');
    Route::put('{id}/unlike', 'GalleriesController@unlike')->where('id', '[0-9]+');
//    // Delete
    Route::delete('{id}', 'GalleriesController@delete')->where('id', '[0-9]+');
});