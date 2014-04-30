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

// Display all SQL executed in Eloquent
Event::listen('illuminate.query', function($sql, $bindings)
{
    foreach ($bindings as $i => $val) {
        $bindings[$i] = "'$val'";
    }
    
    $sql_with_bindings = array_reduce($bindings, function ($result, $item) {
        return substr_replace($result, $item, strpos($result, '?'), 1);
    }, $sql);

    // var_dump($sql_with_bindings);
 // Log::write('info', $sql_with_bindings);
});

Route::get('/', function()
{
	return View::make('layouts.login');
});

// Route::get('/artisan/{command}', function($command){
// 	return Artisan::call($command);
// });

// Route::get('migrate/install', function(){
// 	echo '<br>init migrate:install...';
// 	Artisan::call('migrate:install');
// 	echo 'done migrate:install';
// });

// Route::get('migrate/drop/{table}', function($table){
// 	Schema::drop($table);
// 	return 'Table '. $table .' has dropped.';
// });

// // Use |(pine) instead of / 
// Route::get('migrate/{bench?}', function($bench=null){
// 	if($bench)
// 	{
// 		$b = str_replace('|', '/', $bench);
// 		echo '<br>migrating bench '. $b .'...';
// 		Artisan::call('migrate', ['--bench'=> $b]);
// 		echo 'done migrate bench '. $b;
// 	}
// 	else
// 	{
// 		Artisan::call('migrate');
// 		echo 'done migrate';
// 	}
// });

Route::get('test', function(){

	return App::getBindings();
});

// Route::get('image', function(){
// 	$picture = Input::get('picture', null);

// 	if($picture) return '<img src="data:image/jpeg;base64,'.base64_encode(file_get_contents($picture)).'" />';

// 	return 'error';
// });

Route::any('v1/login', function()
{	
	return View::make('layouts.login');
});