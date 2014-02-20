<?php

    Route::group(array('prefix'=>'api/v1'), function(){
        Route::get('galleries/fields', function(){
            return Response::fields('galleries');
        });
        Route::get('galleries/{id}/medias', 'Kitti\\Galleries\\Controllers\\GalleriesController@showMedias');
        Route::post('galleries/{id}/delete', 'Kitti\\Galleries\\Controllers\\GalleriesController@delete');
        Route::get('galleries/{type}/{id}', 'Kitti\\Galleries\\Controllers\\GalleriesController@showByOwner');
        Route::resource('galleries', 'Kitti\\Galleries\\Controllers\\GalleriesController');
    });