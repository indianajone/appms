<?php namespace Indianajone\Applications;

use DB, Cache, Carbon\Carbon;
use Max\User\Models\User;

class Appl
{

	public function genKey()
	{
		return str_random(32);
	}

	public function getAppIDByKey($key)
	{
		if($key)
		{
			return DB::table('applications')->where('appkey', $key)->first()->id;
			// $expiresAt = Carbon::now()->addMinutes(1);

			// $app = Cache::remember('appkey', $expiresAt, function() use($key)
			// {
			//     return DB::table('applications')->where('appkey', $key)->first();
			// });

			// return $app->id;
		}		
		
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