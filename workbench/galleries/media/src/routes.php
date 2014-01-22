<?php
Route::group(array('prefix' => 'medias'), function(){
    Route::get('fields', 'MediasController@fields');
    Route::get('{id}', 'MediasController@lists')->where('id', '[0-9]+');
    Route::get('{id}/like', 'MediasController@getLike')->where('id', '[0-9]+');
//    Route::get('{id}/medias', 'GalleriesController@medias')->where('id', '[0-9]+');
//    Route::get('{content_type}/{content_id}', 'GalleriesController@unlike');
    // curl -X POST http://localhost/dev/appms/public/article -d appkey=appkey -d p=p
    // http://localhost/dev/appms/public/articles?appkey=1&user_id=1&title=1&content=1&gallery_id=10&pre_title=1&picture=1&teaser=1&wrote_by=1&tags=1&categories=1,2,3,4,5
    // ADD
    Route::post('/', 'MediasController@create');
    // Update
    Route::put('{id}', 'MediasController@update')->where('id', '[0-9]+');
    Route::post('{id}/like', 'MediasController@like')->where('id', '[0-9]+');
    Route::post('{id}/unlike', 'MediasController@unlike')->where('id', '[0-9]+');
//    // Delete
    Route::delete('{id}', 'MediasController@delete')->where('id', '[0-9]+');
});