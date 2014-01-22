<?php

Route::group(array('prefix' => 'articles'), function(){
    // GET
    Route::get('fields','ArticlesController@fields');
    Route::get('/','ArticlesController@lists');
    Route::get('{id}','ArticlesController@listsId')->where('id', '[0-9]+');
    Route::get('/find','ArticlesController@getFind');
    Route::get('{id}/like', 'ArticlesController@like')->where('id', '[0-9]+');
    Route::get('{id}/unlike', 'ArticlesController@unlike')->where('id', '[0-9]+');
    
    // curl -X POST http://localhost/dev/appms/public/article -d appkey=appkey -d p=p
    // ADD
    Route::post('/', 'ArticlesController@create');
    Route::post('{id}/like', 'ArticlesController@like')->where('id', '[0-9]+');
    Route::post('{id}/unlike', 'ArticlesController@unlike')->where('id', '[0-9]+');
    // Update
    Route::put('{id}', 'ArticlesController@update')->where('id', '[0-9]+');
    // Delete
    Route::delete('{id}','ArticlesController@delete')->where('id', '[0-9]+');
});