<?php

	Route::group(array('prefix' => 'api/v1'), function() {
		/**
		*
		*	#TODO:
		*	- Need Appkey filter.
		*	- Need Get fields function.
		*
		**/
		Route::get('categories/fields', function(){
			return Response::fields('categories');
		});
		Route::any('categories/{id}/delete', 'Indianajone\\Categories\\Controllers\\ApiCategoryController@delete');
		Route::resource('categories', 'Indianajone\\Categories\\Controllers\\ApiCategoryController');
	});