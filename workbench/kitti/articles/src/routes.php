<?php
// Route::group(array('prefix' => 'api/v1/articles'), function(){
//     Route::get('fields', array('before' => 'chk_appkey2', 'uses' => 'Kitti\\Articles\\Controllers\\ArticleController@fields'));
//     Route::get('/', array('before' => 'chk_appkey2', 'uses' => 'Kitti\\Articles\\Controllers\\ArticleController@lists'));
//     Route::get('{id}', array('before' => 'chk_appkey2', 'uses' => 'Kitti\\Articles\\Controllers\\ArticleController@listsId'))->where('id', '[0-9]+');
//     Route::get('/find', array('before' => 'chk_appkey2', 'uses' => 'Kitti\\Articles\\Controllers\\ArticleController@find'));
//     Route::get('{id}/like', array('before' => 'chk_appkey2', 'uses' => 'Kitti\\Articles\\Controllers\\ArticleController@like'))->where('id', '[0-9]+');
//     Route::get('{id}/unlike', array('before' => 'chk_appkey2', 'uses' => 'Kitti\\Articles\\Controllers\\ArticleController@unlike'))->where('id', '[0-9]+');

//     Route::post('/', array('before' => 'chk_appkey2', 'uses' => 'Kitti\\Articles\\Controllers\\ArticleController@create'));
//     Route::post('{id}/like', array('before' => 'chk_appkey2', 'uses' => 'Kitti\\Articles\\Controllers\\ArticleController@like'))->where('id', '[0-9]+');
//     Route::post('{id}/unlike', array('before' => 'chk_appkey2', 'uses' => 'Kitti\\Articles\\Controllers\\ArticleController@unlike'))->where('id', '[0-9]+');

//     Route::put('{id}', array('before' => 'chk_appkey2', 'uses' => 'Kitti\\Articles\\Controllers\\ArticleController@update'))->where('id', '[0-9]+');
//     Route::delete('{id}', array('before' => 'chk_appkey2', 'uses' => 'Kitti\\Articles\\Controllers\\ArticleController@delete'))->where('id', '[0-9]+');
// });
    Route::group(array('prefix'=>'api/v1'), function(){
        Route::any('articles/fields', function(){
            return Response::fields('articles');
        });

        Route::any('articles/find', 'Kitti\\Articles\\Controllers\\ArticlesController@showFind');
        Route::any('articles/{id}/like', 'Kitti\\Articles\\Controllers\\ArticlesController@saveLike');
        Route::any('articles/{id}/unlike', 'Kitti\\Articles\\Controllers\\ArticlesController@saveUnlike');

        // Route::any('articles/{id}' , 'Kitti\\Articles\\Controllers\\ArticlesController@edit');
        Route::resource('articles', 'Kitti\\Articles\\Controllers\\ArticlesController');
    });