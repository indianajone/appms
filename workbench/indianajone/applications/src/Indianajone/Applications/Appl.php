<?php namespace Indianajone\Applications;

use \DB;

class Appl
{

	public function genKey()
	{
		return str_random(32);
	}

	public function getAppIDByKey($key)
	{
		if($key)
			return DB::table('applications')->where('appkey', $key)->first()->id;
		return null;
	}

	/*==========  Example on how to convent back to unixtime  ==========*/
	
	// public function getUnixtimeAttribute()
	// {
	// 	$format = \Input::get('date_format', null);
	// 	$time = Carbon::createFromTimeStamp($this->attributes['created_at'])->format($format);
	// 	return Carbon::createFromFormat($format, $time, \Config::get('app.timezone'))->timestamp;
	// }
}