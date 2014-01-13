<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

Route::get('/', function()
{
	return View::make('hello');
});

// Route::get('/users', function(){
// 	return User::find(1);
// });

Route::get('/roles', function(){
	return Indianajone\RolesAndPermissions\Role::all();
});

Route::get('/permissions', function(){
	return Indianajone\RolesAndPermissions\Permission::all();
});

// / Route::get('/roles/create', function(){
// 	$r = new \Indianajone\RolesAndPermissions\Role;
// 	$r->name = 'user_all';
// 	$r->display_name = 'Full access User Plugin';
// 	$r->save();
// });

// Route::get('/permissions/attach/{role_id}', function($id){
// 	$role = Indianajone\RolesAndPermissions\Role::find($id);
// 	$role->perms()->sync(
// 		array(1)
// 	);
// });

// Route::get('/permissions/create', function(){
// 	$p = new \Indianajone\RolesAndPermissions\Permission;
// 	$p->name = 'user_all';
// 	$p->display_name = 'Full access User Plugin';
// 	$p->method_id = 1;
// 	$p->save();
// });

Route::get('/auth/login', function(){
	$user = array(
        'email' => Input::get('email'),
        'password' => Input::get('password')
    );

	if(!Auth::check()) {
		Auth::attempt($user);
	}
	else {
		return 'you are already logedin.';
	}

	$user = User::find(Auth::user()->id)->with('roles')->get();

	return $user;
});

Route::get('/auth/logout', function(){
	if(Auth::check()) {
		Auth::logout();
		return 'logout success.';
	}
	else {
		return 'you are not logedin.';
	}
});


Route::get('/users/{id?}', function($id=1){
	$user = User::find(1);
	$user->roles->with('permits');	
	
	return $user;
});