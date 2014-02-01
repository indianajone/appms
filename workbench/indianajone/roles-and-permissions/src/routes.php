<?php
	
<<<<<<< HEAD
	Route::get('api/v1/roles', function(){
		return 'Roles';
	});

	Route::get('api/v1/permissions', function(){
		return 'Permissions';
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
		// Roles
<<<<<<< HEAD
		Route::get('roles/{id}/attach', 'Indianajone\\RolesAndsPermissions\\Controllers\\RoleController@attachPermissions');
		Route::post('roles/{id}/delete', 'Indianajone\\RolesAndsPermissions\\Controllers\\RoleController@delete');
		Route::resource('roles', 'Indianajone\\RolesAndsPermissions\\Controllers\\RoleController');
		// Permissions
=======
		Route::get('roles/fields', function(){
			return Response::fields('roles');
		});
		Route::any('roles/{id}/attach', 'Indianajone\\RolesAndsPermissions\\Controllers\\RoleController@attachPermissions');
		Route::post('roles/{id}/delete', 'Indianajone\\RolesAndsPermissions\\Controllers\\RoleController@delete');
		Route::resource('roles', 'Indianajone\\RolesAndsPermissions\\Controllers\\RoleController');
		// Permissions
		Route::get('permissions/fields', function(){
			return Response::fields('permissions');
		});
>>>>>>> best
		Route::post('permissions/{id}/delete', 'Indianajone\\RolesAndsPermissions\\Controllers\\PermissionController@delete');
		Route::resource('permissions', 'Indianajone\\RolesAndsPermissions\\Controllers\\PermissionController');
	});