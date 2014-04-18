<?php

    Route::group(array('prefix'=>'api/v1/'), function(){
        Route::get('medias/fields', function(){
            return Response::fields('medias');
        });

        Route::any('medias/{id}/delete', 'Kitti\\Medias\\Controllers\ApiMediasController@delete');
        Route::resource('medias', 'Kitti\\Medias\\Controllers\ApiMediasController');
    });