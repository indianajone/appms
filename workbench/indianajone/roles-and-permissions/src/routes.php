<?php
	
	Route::get('api/v1/roles', function(){
		return 'Roles';
	});

	Route::get('api/v1/permissions', function(){
		return 'Permissions';
	});

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
		Route::post('permissions/{id}/delete', 'Indianajone\\RolesAndsPermissions\\Controllers\\PermissionController@delete');
		Route::resource('permissions', 'Indianajone\\RolesAndsPermissions\\Controllers\\PermissionController');
	});