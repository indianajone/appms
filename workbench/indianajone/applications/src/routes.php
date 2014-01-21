<?php
	
	Route::get('api/v1/apps', function(){
		return 'Applications';
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
		Route::post('apps/{id}/delete', 'Indianajone\\Applications\\Controllers\\ApplicationController@delete');
		Route::resource('apps', 'Indianajone\\Applications\Controllers\\ApplicationController');
	});