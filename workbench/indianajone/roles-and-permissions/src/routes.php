<?php

	// Route::filter('appkey', function() {
	//         $key = Input::get('appkey', null);
	//         if($key == null)
	//                 return  Response::missing('The appkey field is required.');
	//         elseif ($key != 1234)
	//                 return  Response::invalid('appkey');
	// });

	Route::group(array('prefix' => 'api/v1'), function() {
		Route::get('/', function(){
	    	return 'Hello';
		});
		// Route::group(array('before' => 'appkey', function() {
		// // 	// Route::get('users/fields', 'Indianajone\\Users\\Controllers\\UsersController@fields');
			Route::resource('roles', 'Indianajone\\RolesAndsPermissions\\Controllers\\RoleController');
		// });
	});