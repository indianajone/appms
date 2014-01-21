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
	return 'hello'; // View::make('hello');
});

Route::get('/users', function(){
	return User::find(1)->with('apps')->get();
});

// Route::get('/roles', function(){
// 	return Indianajone\RolesAndPermissions\Role::all();
// });

// Route::get('/permissions', function(){
// 	return Indianajone\RolesAndPermissions\Permission::all();
// });

// // / Route::get('/roles/create', function(){
// // 	$r = new \Indianajone\RolesAndPermissions\Role;
// // 	$r->name = 'user_all';
// // 	$r->display_name = 'Full access User Plugin';
// // 	$r->save();
// // });

// // Route::get('/permissions/attach/{role_id}', function($id){
// // 	$role = Indianajone\RolesAndPermissions\Role::find($id);
// // 	$role->perms()->sync(
// // 		array(1)
// // 	);
// // });

// // Route::get('/permissions/create', function(){
// // 	$p = new \Indianajone\RolesAndPermissions\Permission;
// // 	$p->name = 'user_all';
// // 	$p->display_name = 'Full access User Plugin';
// // 	$p->method_id = 1;
// // 	$p->save();
// // });

Route::get('migrate/install', function(){
	echo '<br>init migrate:install...';
	Artisan::call('migrate:install');
	echo 'done migrate:install';
});

Route::get('migrate/drop/{table}', function($table){
	Schema::drop($table);
	return 'Table '. $table .' has dropped.';
});

// Use |(pine) instead of / 
Route::get('migrate/{bench?}', function($bench){
	if(isset($bench))
	{
		$b = str_replace('|', '/', $bench);
		echo '<br>migrating bench '. $b .'...';
		Artisan::call('migrate', ['--bench'=> $b]);
		echo 'done migrate bench '. $b;
	}
	else
	{
		Artisan::call('migrate');
		echo 'done migrate';
	}
});

// Route::get('/auth/login', function(){
// 	$user = array(
//         'email' => Input::get('email'),
//         'password' => Input::get('password')
//     );

// 	if(!Auth::check()) {
// 		Auth::attempt($user);
// 	}
// 	else {
// 		return 'you are already logedin.';
// 	}

// 	$user = User::find(Auth::user()->id)->with('roles')->get();

// 	return $user;
// });

// Route::get('/auth/logout', function(){
// 	if(Auth::check()) {
// 		Auth::logout();
// 		return 'logout success.';
// 	}
// 	else {
// 		return 'you are not logedin.';
// 	}
// });
Route::get('/users/{id?}/children', function($id=1){

	
	$user = User::find($id);
	$user->children;
	// var_dump($users->children->count());
	// dd($users->children()->getResults());
	// if($users['children'] ?: null);
	// dd($user->children->toArray());
	// dd($user->children->modelKeys());

	return $user;
});

Route::get('/users/create', function(){
	User::create(
		array(
			'parent_id' => 4,
			'first_name' => 'Ti',
			'last_name' => 'sasas',
			'email' => 'sada@job.com',
			'username'=>'ti',
			'password'=> Hash::make('test')
		)
	);
});

Route::get('/users/{id?}', function($id=1){
	$user = User::find(1)->with('roles.permits')->get();
	// $user->roles->each(function($role) {
	// 	$role->permits;	
	// });
	return $user;
});