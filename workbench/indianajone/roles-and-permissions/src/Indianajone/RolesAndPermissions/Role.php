<?php namespace Indianajone\RolesAndPermissions;

use Zizaco\Entrust\EntrustRole;

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
        return $this->belongsToMany('User', 'user_roles');
    }
}