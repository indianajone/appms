<?php namespace Indianajone\Applications;

use BaseModel;

class Application extends BaseModel
{
	/**
	* The database table used by the model.
	*
	* @var string
	**/
	protected $table = 'applications';

	/**
	* The attributes excluded from the model's JSON form.
	*
	* @var array
	**/
	protected $hidden = array('app_key','user_id');

	/**
	*
	* Application Owner
	*
	* @return User Model
	**/
	public function user()
	{
		$this->belongTo('User', 'user_id');
	}
}