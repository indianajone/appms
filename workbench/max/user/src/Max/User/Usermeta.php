<?php namespace Max\User\Models;

class Usermeta extends \Eloquent
{
	protected $table = 'user_meta';

	protected $fillable = array('user_id', 'meta_key', 'meta_value');
	
	public 	$timestamps = false;
}