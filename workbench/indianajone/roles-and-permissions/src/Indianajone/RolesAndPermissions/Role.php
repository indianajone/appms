<?php namespace Indianajone\RolesAndPermissions;

use Carbon\Carbon;

class Role extends \Zizaco\Entrust\EntrustRole {

    protected $hidden = array('pivot');

    /**
     * Override Many-to-Many relations with Permission
     * named perms as permissions is already taken.
     */
    public function permits()
    {
        return $this->perms();
    }

    public function perms()
    {
        // To maintain backwards compatibility we'll catch the exception if the Permission table doesn't exist.
        // TODO remove in a future version
        try {
            return $this->belongsToMany('Indianajone\RolesAndPermissions\Permission');
        } catch(Execption $e) {}
    }
    
	/**
     * Many-to-Many relations with Users
     */
    public function users()
    {
        return $this->belongsToMany('Max\User\Models\User', 'user_roles');
    }
}