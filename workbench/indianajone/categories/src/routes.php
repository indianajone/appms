<?php

	Route::get('api/v1/categories', function(){
		return 'Categories';
	});

	Route::group(array('prefix' => 'api/v1'), function() {
		/**
		*
		*	TODO:
		*	- Need Appkey filter.
		*	- Need Get fields function.
		*
		**/
		// Apps
		// Route::post('apps/{id}/delete', 'Indianajone\\Applications\\Controllers\\ApplicationController@delete');
		Route::get('categories/fields', function(){
			return Response::fields('categories', Input::get('format', 'json'));
		});
		Route::resource('categories', 'Indianajone\\Categories\\Controllers\\CategoryController');
	});