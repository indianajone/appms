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

    public function formatTime($value)
    {
    	$format = \Input::get('date_format', null);
		return $format ? Carbon::createFromTimeStamp($value, \Config::get('app.timezone'))->format($format) : $value;    
    }

    public function getCreatedAtAttribute($value)
	{
		return $this->formatTime($value);
	}

	public function getUpdatedAtAttribute($value)
	{
		return $this->formatTime($value);    
	}

	public function getPublishAtAttribute($value)
	{
		return $this->formatTime($value); 
	}

	public function scopeTime($query, $field)
	{
		$format = \Input::get('date_format', null);
	    $from = \Input::get($field);
	    if($format)
	    {
	    	$time = Carbon::createFromFormat($format, $from, \Config::get('app.timezone'));
	    	return $query->where($field, '>=', $time->timestamp);
	    }
	    else return $query->where($field, '>=', $from);
	}

	public function scopeApp($query)
    {
        return $query->whereAppId(Appl::getAppIdByKey(Input::get('appkey')));
    }

    public function scopeActive($query)
    {
        return $query->whereStatus(1);
    }
}