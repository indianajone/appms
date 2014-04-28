<?php namespace Indianajone\Applications;

class ApplicationMeta extends \Eloquent
{
	protected $table = 'application_meta';	

	protected $fillable = array('app_id', 'meta_key', 'meta_value');

	protected $hidden = array('id', 'app_id');

	public $timestamps= false;

	/**
	 * Check if protected meta name
	 *
	 * @param $name meta name
	 * @return boolean
	 */
	public function is_protected($name)
	{
		return  ( '_' == $name[0] );
	}
}