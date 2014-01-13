<?php

	Route::group(array('prefix' => 'api/v1'), function() {
		/**
		*
		*	TODO:
		*	- Need Appkey filter.
		*	- Need Get fields function.
		*
		**/
		
		Route::post('roles/{id}/delete', 'Indianajone\\RolesAndsPermissions\\Controllers\\RoleController@delete');
		Route::resource('roles', 'Indianajone\\RolesAndsPermissions\\Controllers\\RoleController');
	});