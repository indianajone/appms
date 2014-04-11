<?php

Route::group(array('prefix' => 'api/v1'), function(){

	Route::get('articles/fields', function(){
		return Response::fields('articles');
	});
	Route::post('articles/{id}/delete', 'Kitti\\Articles\\Controllers\\ArticleController@delete');
	Route::resource('articles', 'Kitti\\Articles\\Controllers\\ArticleController');

	Route::any('articles/{id}/share', 'Kitti\\Articles\\Controllers\\ShareArticleController@index');

});