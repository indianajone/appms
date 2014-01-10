<?php namespace Indianajone\RolesAndPermissions;

use Zizaco\Entrust\EntrustPermission;

class Permission extends EntrustPermission {

	protected $hidden = array('pivot', 'method_id');

	/** 
	 * Override getDateFormat to unixtime stamp.
	 * @return String
	 */
	protected function getDateFormat()
    {
        return 'U';
    }

    public function roles()
    {
    	$this->belongsToMany('Indianajone\RolesAndPermissions\Role', 'permission_role');
    }

}