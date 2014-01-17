<?php

Route::group(array('prefix' => 'articles'), function(){
    // GET
    Route::get('fields','ArticlesController@getFields');
    Route::get('/','ArticlesController@getList');
    Route::get('/find','ArticlesController@getFind');
    Route::get('{id}/like', 'ArticlesController@like')->where('id', '[0-9]+');
    Route::get('{id}/unlike', 'ArticlesController@unlike')->where('id', '[0-9]+');
    // curl -X POST http://localhost/dev/appms/public/article -d appkey=appkey -d p=p
    // http://localhost/dev/appms/public/articles?appkey=1&user_id=1&title=1&content=1&gallery_id=10&pre_title=1&picture=1&teaser=1&wrote_by=1&tags=1&categories=1,2,3,4,5
    // ADD
    
    Route::post('/', 'ArticlesController@createArticles');

    // Update
    Route::put('/', 'ArticlesController@updateArticles');
    // Delete
    Route::delete('{id}','ArticlesController@deleteArticles')->where('id', '[0-9]+');
    
});