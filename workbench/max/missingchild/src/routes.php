<?php

	Route::group(array('prefix'=>'api/v1'), function(){

		Route::get('missingchilds/fields', function(){
			return Response::fields('missingchilds');
		});

		Route::any('missingchilds/{id}/delete', 'Max\\Missingchild\\Controllers\\MissingchildController@delete');
		// Route::any('missingchilds/{id}/clues', 'Max\\Missingchild\\Controllers\\MissingchildController@clues');
		Route::any('missingchilds/{id}/clues/attach', 'Max\\Missingchild\\Controllers\\MissingchildController@attachArticles');
		Route::any('missingchilds/{id}/clues/detach', 'Max\\Missingchild\\Controllers\\MissingchildController@detachArticles');
		Route::resource('missingchilds', 'Max\\Missingchild\\Controllers\\MissingchildController');

	});