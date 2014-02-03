<?php

	Route::group(array('prefix'=>'api/v1'), function(){

		Route::get('missingchilds/fields', function(){
			return Response::fields('missingchilds');
		});

		Route::post('missingchilds/{id}/delete', 'Max\\Missingchild\\Controllers\\MissingchildController@delete');
		Route::resource('missingchilds', 'Max\\Missingchild\\Controllers\\MissingchildController');

	});