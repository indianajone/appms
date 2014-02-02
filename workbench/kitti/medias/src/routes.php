<?php
Route::group(array('prefix'=>'api/v1'), function(){
    Route::any('medias/fields', function(){
        return Response::fields('medias');
    });

    Route::get('medias/{id}/likes','Kitti\\Medias\\Controllers\\MediasController@showLike');
    Route::post('medias/{id}/like','Kitti\\Medias\\Controllers\\MediasController@createLike');
    Route::post('medias/{id}/unlike','Kitti\\Medias\\Controllers\\MediasController@deleteLike');

    Route::resource('medias', 'Kitti\\Medias\\Controllers\\MediasController');
});