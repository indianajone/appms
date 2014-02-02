<?php
    Route::group(array('prefix'=>'api/v1'), function(){
        Route::any('articles/fields', function(){
            return Response::fields('articles');
        });

        Route::any('articles/find', 'Kitti\\Articles\\Controllers\\ArticlesController@showFind');
        Route::any('articles/{id}/like', 'Kitti\\Articles\\Controllers\\ArticlesController@createLike');
        Route::any('articles/{id}/unlike', 'Kitti\\Articles\\Controllers\\ArticlesController@deleteLike');
        Route::resource('articles', 'Kitti\\Articles\\Controllers\\ArticlesController');
    });