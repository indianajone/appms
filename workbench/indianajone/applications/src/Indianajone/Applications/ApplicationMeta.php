<?php namespace Indianajone\Applications;

class ApplicationMeta extends \Eloquent
{
	protected $table = 'application_meta';	
	protected $hidden = array('id', 'app_id');
}