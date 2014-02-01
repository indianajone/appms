<?php
	
<<<<<<< HEAD
	Route::get('api/v1/apps', function(){
		return 'Applications';
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
		// Apps
<<<<<<< HEAD
		Route::post('apps/{id}/delete', 'Indianajone\\Applications\\Controllers\\ApplicationController@delete');
=======
		Route::get('apps/fields', function(){
			return Response::fields('applications');
		});
		Route::get('apps/{id}/delete', 'Indianajone\\Applications\\Controllers\\ApplicationController@delete');
>>>>>>> best
		Route::resource('apps', 'Indianajone\\Applications\Controllers\\ApplicationController');
	});