<?php namespace Indianajone\Share\Models;

class Share extends \Eloquent 
{
	protected $rules = array(
		'share' => array(
			'appkey'		=> 'required|exists:applications',
			'content_type' 	=> 'required|in:article,gallery',
			'content_id'	=> 'required'
		)
	);

	use \BaseModel;
}