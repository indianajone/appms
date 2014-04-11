<?php namespace Indianajone\Applications;

use DB, Input;
use Indianajone\Applications\ApplicationMeta as Meta;

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
			/**  
			  Need to implement better caching. 
			 */
			return DB::table('applications')->where('appkey', $key)->remember(1)->first()->id;
		}		
		
		return null;
	}

	public function getMeta($key, $appkey=null)
	{
		if( is_null($appkey) ) $appkey = Input::get('appkey');

		$app_id = $this->getAppIDByKey($appkey);
		
		return Meta::whereAppId($app_id)->whereMetaKey($key)->first()->meta_value; 
	}

	public function setMeta($key, $value, $appkey=null)
	{
		if( is_null($appkey) ) $appkey = Input::get('appkey');

		$app_id = $this->getAppIDByKey($appkey);

		$meta = Meta::whereAppId($app_id)->whereMetaKey($key)->first();

		$meta->meta_value = $value;

		$meta->save();
	}
}