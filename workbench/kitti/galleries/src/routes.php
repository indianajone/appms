<?php

    Route::group(array('prefix'=>'api/v1'), function(){
        Route::any('galleries/fields', function(){
            return Response::fields('galleries');
        });
        Route::any('galleries/{id}/medias', 'Kitti\\Galleries\\Controllers\\GalleriesController@showMedias');
        Route::post('galleries/{id}/delete', 'Kitti\\Galleries\\Controllers\\GalleriesController@delete');
        Route::any('galleries/{type}/{id}', 'Kitti\\Galleries\\Controllers\\GalleriesController@showByOwner');
        Route::resource('galleries', 'Kitti\\Galleries\\Controllers\\GalleriesController');
    });