<?php

    Route::group(array('prefix'=>'api/v1/'), function(){
        Route::get('medias/fields', function(){
            return Response::fields('medias');
        });

        Route::post('delete', 'Kitti\\Medias\\Controllers\MediasController@delete');
        Route::resource('medias', 'Kitti\\Medias\\Controllers\MediasController');
    });