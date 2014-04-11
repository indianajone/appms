<?php namespace Indianajone\Applications;

class ApplicationMeta extends \Eloquent
{
	protected $table = 'application_meta';	

	protected $fillable = array('meta_key', 'meta_value');

	protected $hidden = array('id', 'app_id');

	public $timestamps= false;
}