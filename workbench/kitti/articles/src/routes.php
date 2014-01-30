<?php

Route::group(array('prefix' => 'api/v1'), function(){

	Route::get('articles/fields', function(){
		return Response::fields('articles');
	});

	Route::resource('articles', 'Kitti\\Articles\\Controllers\\ArticleController');

});