<?php namespace Indianajone\RolesAndPermissions;

use Zizaco\Entrust\EntrustRole;
use Carbon\Carbon;

class Role extends EntrustRole {

    protected $hidden = array('pivot');

	/** 
	 * Override getDateFormat to unixtime stamp.
	 * @return String
	 */
	protected function getDateFormat()
    {
        return 'U';
    }

    /**
     * Override Many-to-Many relations with Permission
     * named perms as permissions is already taken.
     */
    public function permits()
    {
        return $this->belongsToMany('Indianajone\RolesAndPermissions\Permission');
    }
    
	/**
     * Many-to-Many relations with Users
     */
    public function users()
    {
        return $this->belongsToMany('Max\User\Models\User', 'user_roles');
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
}