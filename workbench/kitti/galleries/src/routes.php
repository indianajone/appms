<?php
    Route::group(array('prefix'=>'api/v1'), function(){
        Route::any('galleries/fields', function(){
            return Response::fields('galleries');
        });
        Route::get('galleries/{id}/likes','Kitti\\Galleries\\Controllers\\GalleriesController@showLike');
        Route::post('galleries/{id}/like','Kitti\\Galleries\\Controllers\\GalleriesController@createLike');
        Route::post('galleries/{id}/unlike','Kitti\\Galleries\\Controllers\\GalleriesController@deleteLike');
        Route::any('galleries/{id}/medias', 'Kitti\\Galleries\\Controllers\\GalleriesController@showMedias');
        Route::any('galleries/{type}/{id}', 'Kitti\\Galleries\\Controllers\\GalleriesController@showByOwner');

        Route::resource('galleries', 'Kitti\\Galleries\\Controllers\\GalleriesController');
    });