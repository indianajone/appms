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

	public function scopeTime($query, $field)
	{
	    $updated_at = \Input::get($field);
	    $time = Carbon::createFromFormat(\Input::get('date_format'), $updated_at, \Config::get('app.timezone'));
	    return $query->where($field, '>=', $time->timestamp);
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