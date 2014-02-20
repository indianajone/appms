<?php

	Route::group(array('prefix'=>'api/v1'), function(){

		Route::get('missingchilds/fields', function(){
			return Response::fields('missingchilds');
		});

		// Route::post('missingchilds/{id}/delete', 'Max\\Missingchild\\Controllers\\MissingchildController@delete');
		// Route::get('missingchilds/{id}/articles', 'Max\\Missingchild\\Controllers\\MissingchildController@articles');
		// Route::post('missingchilds/{id}/articles', 'Max\\Missingchild\\Controllers\\MissingchildController@createArticles');
		// Route::post('missingchilds/{id}/article/attach', 'Max\\Missingchild\\Controllers\\MissingchildController@attachArticles');
		// Route::post('missingchilds/{id}/article/detach', 'Max\\Missingchild\\Controllers\\MissingchildController@detachArticles');
		Route::resource('missingchilds', 'Max\\Missingchild\\Controllers\\MissingchildController');
		Route::resource('missingchilds.articles', 'Max\\Missingchild\\Controllers\\MissingchildArticleController', array('except' => array('show')));
	});