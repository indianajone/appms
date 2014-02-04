<?php

	Route::group(array('prefix'=>'api/v1'), function(){

		Route::get('missingchilds/fields', function(){
			return Response::fields('missingchilds');
		});

		Route::post('missingchilds/{id}/delete', 'Max\\Missingchild\\Controllers\\MissingchildController@delete');
		Route::post('missingchilds/{id}/clues', 'Max\\Missingchild\\Controllers\\MissingchildController@clues');
		Route::any('missingchilds/{id}/clues/attach', 'Max\\Missingchild\\Controllers\\MissingchildController@attachClue');
		Route::post('missingchilds/{id}/clues/detach', 'Max\\Missingchild\\Controllers\\MissingchildController@detachClue');
		Route::resource('missingchilds', 'Max\\Missingchild\\Controllers\\MissingchildController');

	});