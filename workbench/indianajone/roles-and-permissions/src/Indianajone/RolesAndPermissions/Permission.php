<?php namespace Indianajone\RolesAndPermissions;

use Zizaco\Entrust\EntrustPermission;
use Carbon\Carbon;

class Permission extends EntrustPermission {

   protected $guarded = array('id');
	protected $hidden = array('pivot');
   public static $rules = array();

	use \BaseModel;

	public function rules($action)
	{
	   return static::$rules[$action];
	}

    public function roles()
    {
    	$this->belongsToMany('Indianajone\RolesAndPermissions\Role', 'permission_role');
    }

}