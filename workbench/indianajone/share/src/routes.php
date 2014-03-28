<?php

	Route::group(array('prefix' => 'api/v1'), function() 
	{
		Route::get('share/{provider}', 'Indianajone\Share\Controller\ApiShareController@provider');
	});