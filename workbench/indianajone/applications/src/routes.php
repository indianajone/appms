<?php
	
	Route::group(array('prefix' => 'api/v1'), function() {
		/**
		*
		*	TODO:
		*	- Need Appkey filter.
		*	- Need Get fields function.
		*
		**/
		// Apps
		Route::get('apps/fields', function(){
			return Response::fields('applications');
		});
		Route::post('apps/{id}/delete', 'Indianajone\\Applications\\Controllers\\ApiApplicationController@delete');
		Route::resource('apps', 'Indianajone\\Applications\Controllers\\ApiApplicationController');
	});

	Route::group(array('prefix' => 'v1'), function() {
		/**
		*
		*	TODO:
		*	- Need Appkey filter.
		*	- Need Get fields function.
		*
		**/
		// Apps
		Route::get('apps/fields', function(){
			return Response::fields('applications');
		});
		Route::post('apps/{id}/delete', 'Indianajone\\Applications\\Controllers\\ApplicationController@delete');
		Route::resource('apps', 'Indianajone\\Applications\Controllers\\ApplicationController');
	});