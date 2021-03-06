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

	/**
	* The attributes excluded from the model's JSON form.
	*
	* @var array
	**/
	protected $hidden = array('appkey');


	public static $rules = array(
		'show' => array(
			// 'appkey' => 'required|exists:applications,appkey'
		),
		'create' => array(
			'user_id' 	=> 'required|exists:users,id',
			'name'		=> 'required'
		),
		'update' => array(
			'user_id' => 'required'
		),
		'delete' => array(
			'id' => 'required|exists:applications'
		)
	);

	/**
	*
	* Application Owner
	*
	* @return User Model
	**/
	public function owner()
	{
		return $this->belongsTo('Max\\User\\Models\\User', 'user_id');
	}

	public function genKey()
	{
		return str_random(32);
	}

	public function getAppIDByKey($key)
	{
		return $this->whereAppkey($key)->first();
	}

	/*==========  Example on how to convent back to unixtime  ==========*/
	
	// public function getUnixtimeAttribute()
	// {
	// 	$format = \Input::get('date_format', null);
	// 	$time = Carbon::createFromTimeStamp($this->attributes['created_at'])->format($format);
	// 	return Carbon::createFromFormat($format, $time, \Config::get('app.timezone'))->timestamp;
	// }
}