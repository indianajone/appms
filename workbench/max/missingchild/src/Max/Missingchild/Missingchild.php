<?php namespace Max\Missingchild\Models;

class Missingchild extends \BaseModel 
{
	protected $table = 'missingchilds';

	public static $rules = array(
        'show' => array(
            'appkey'    => 'required|exists:applications,appkey',
        ),
        'create' => array(
        	'appkey'			=> 'required|exists:applications,appkey',
        	'first_name'		=> 'required',
        	'last_name'			=> 'required',
        	'lost_age'			=> 'required|integer',
        	'place_of_missing' 	=> 'required',
        	'missing_date'		=> 'required',
        	'report_date'		=> 'required',
        	'user_id'			=> 'exists:users,id',
        	'order'				=> 'integer'
        ),
        'update' => array(),
        'delete' => array()
    );
}
