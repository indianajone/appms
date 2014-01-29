<?php namespace Indianajone\RolesAndPermissions;

use Zizaco\Entrust\EntrustPermission;
use Carbon\Carbon;

class Permission extends EntrustPermission {

    protected $guarded = array('id');
	protected $hidden = array('pivot');
    public static $rules = array();

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

    public function roles()
    {
    	$this->belongsToMany('Indianajone\RolesAndPermissions\Role', 'permission_role');
    }

}