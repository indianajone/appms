<?php

<<<<<<< HEAD
	Route::get('api/v1/categories', function(){
		return 'Categories';
	});

=======
>>>>>>> best
	Route::group(array('prefix' => 'api/v1'), function() {
		/**
		*
		*	TODO:
		*	- Need Appkey filter.
		*	- Need Get fields function.
		*
		**/
<<<<<<< HEAD
		// Apps
		// Route::post('apps/{id}/delete', 'Indianajone\\Applications\\Controllers\\ApplicationController@delete');
=======
		Route::get('categories/fields', function(){
			return Response::fields('categories');
		});
		Route::get('categories/{id}/delete', 'Indianajone\\Categories\\Controllers\\CategoryController@delete');
>>>>>>> best
		Route::resource('categories', 'Indianajone\\Categories\\Controllers\\CategoryController');
	});