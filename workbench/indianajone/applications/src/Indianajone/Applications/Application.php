<?php namespace Indianajone\Applications;


class Application extends \BaseModel
{
	/**
	* The database table used by the model.
	*
	* @var string
	**/
	protected $table = 'applications';

	protected $guarded = array('id', 'appkey');


	public static $rules = array(
		'update' => array(
			'name' => 'required',
			'user_id' => 'required'
		),
		'delete' => array(
			'id' => 'required|exists:applications'
		)
	);

	/**
	* The attributes excluded from the model's JSON form.
	*
	* @var array
	**/
	protected $hidden = array('appkey');

	/**
	*
	* Application Owner
	*
	* @return User Model
	**/
	public function owner()
	{
		return $this->belongsTo('User', 'user_id');
	}

	public function check($key)
	{
		return $this->appkey();
	}

	public function getRules()
	{
		return $this->rules;
	}

	public function getAppIDByKey($key)
	{
		return $this->where('appkey', $key)->first();
	}
}