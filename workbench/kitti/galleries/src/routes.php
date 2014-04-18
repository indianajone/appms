<?php

    Route::group(array('prefix'=>'api/v1'), function(){
        Route::get('galleries/fields', function(){
            return Response::fields('galleries');
        });
        Route::get('galleries/{id}/medias', 'Kitti\\Galleries\\Controllers\\ApiGalleriesController@showMedias');
        Route::any('galleries/{id}/delete', 'Kitti\\Galleries\\Controllers\\ApiGalleriesController@delete');
        Route::resource('galleries', 'Kitti\\Galleries\\Controllers\\ApiGalleriesController');
    });