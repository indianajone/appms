<?php

	Route::group(array('prefix' => 'api/v1'), function() {
		/**
		*
		*	TODO:
		*	- Need Appkey filter.
		*	- Need Get fields function.
		*
		**/
		// Roles
		Route::post('roles/{id}/delete', 'Indianajone\\RolesAndsPermissions\\Controllers\\RoleController@delete');
		Route::resource('roles', 'Indianajone\\RolesAndsPermissions\\Controllers\\RoleController');

		// Permissions
		Route::post('permisions/{id}/delete', 'Indianajone\\RolesAndsPermissions\\Controllers\\PermissionController@delete');
		Route::resource('permisions', 'Indianajone\\RolesAndsPermissions\\Controllers\\PermissionController');
	});