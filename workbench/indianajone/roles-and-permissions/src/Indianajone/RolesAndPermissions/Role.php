<?php namespace Indianajone\RolesAndPermissions;

use Carbon\Carbon;

class Role extends \Zizaco\Entrust\EntrustRole 
{
    use \BaseModel;

    protected $guarded = array('id');
    protected $hidden = array('pivot');

    public static $rules = array(
        'show'      => array(
            'id'    => 'required|exists:roles'
        ),
        'create'    => array(
            'name'  => 'required|unique:roles' 
        ),
        'attach'    => array(
            'permission_id' => 'required|existsloop:permissions,id'
        )
    );

    public function rules($action)
    {
        return static::$rules[$action];
    }

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
       return $this->belongsToMany('Indianajone\RolesAndPermissions\Permission');
    }
    
	/**
     * Many-to-Many relations with Users
     */
    public function users()
    {
        return $this->belongsToMany('Max\User\Models\User', 'user_roles');
    }

    /**
     * Search useing keyword.
     *
     * @param $fields
     * @return Illuminate\Database\Query\Builder
     */
    public function scopeSearch($query)
    {
        return $this->keywords(array('name'));
    }

    /**
     * Before delete all constrained foreign relations
     *
     * @param bool $forced
     * @return bool
     */
    public function beforeDelete( $forced = false )
    {
        try {
            \DB::table('user_roles')->where('role_id', $this->id)->delete();
            \DB::table('permission_role')->where('role_id', $this->id)->delete();
        } catch(Execption $e) {}

        return true;
    }
}