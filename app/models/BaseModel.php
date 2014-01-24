<?php

use Carbon\Carbon;

class BaseModel extends Eloquent 
{
	/** 
	 * Override getDateFormat to unixtime stamp.
	 * @return String
	 */
	protected function getDateFormat()
    {
        return 'U';
    }

    public function getCreatedAtAttribute($value)
	{
		$format = \Input::get('date_format', null);
		return $format ? Carbon::createFromTimeStamp($value, \Config::get('app.timezone'))->format($format) : $value;     
	}

	public function getUpdatedAtAttribute($value)
	{
		$format = \Input::get('date_format', null);
		return $format ? Carbon::createFromTimeStamp($value, \Config::get('app.timezone'))->format($format) : $value;     
	}
}