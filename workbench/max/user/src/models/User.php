<?php namespace Max\User\Models;

use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableInterface;
use Zizaco\Entrust\HasRole;
use \BaseModel;

class User extends BaseModel implements UserInterface, RemindableInterface 
{
    public static $rules = array(
        'chk_id' => array(
            'id' => 'required|exists:users'
        ),
        'create' => array(
            'username'  => 'required|unique:users,username',
            'password'  => 'required',
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email|unique:users,email',
        ),
        'update' => array(
            'email' => 'required|email|exists:users'
        ),
        'delete' => array(
            'id' => 'required|exists:users'
        ),
        'fields' => array(
            'table' => 'required'
        ),
        'login' => array(
            'username'  => 'required|exists:users,username',
            'password'  => 'required'
        ),
        'resetPwd' => array(
            'username'    => 'required|exists:users,username',
            'password' => 'required',
            'new_password' => 'required'
        ),
        'manage_role' => array(
            'id' => 'required|exists:users',
            'action' => 'required',
            'role_id' => 'required|existloop:roles,id'
        )
    );

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = array('password', 'parent_id');

    protected $guarded = array('id');

    // protected $appends = array('test');

    use HasRole; // Add this trait to your user model


    /**
     * Get the unique identifier for the user.
     *
     * @return mixed
     */
    public function getAuthIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Get the password for the user.
     *
     * @return string
     */
    public function getAuthPassword()
    {
        return $this->password;
    }

    /**
     * Get the e-mail address where password reminders are sent.
     *
     * @return string
     */
    public function getReminderEmail()
    {
        return $this->email;
    }

    public function checkPassword($password)
    {
        return \Hash::check($password, $this->getAuthPassword());
    }

    public function apps(){
        return $this->hasMany('Indianajone\\Applications\\Application', 'user_id');
    }

    /**
     * Many-to-Many relations with Role
     */
    public function roles()
    {
        return $this->belongsToMany('Indianajone\RolesAndPermissions\Role', 'user_roles');
    }

    public function parent()
    {
        return $this->belongsTo('Max\User\Models\User', 'parent_id');
    }

    public function children()
    {
        return $this->hasMany('Max\User\Models\User', 'parent_id');
    }

    public function getChildrenId($ids=array())
    {
        $ids[] = $this->id;
        
        if($this->children)
        {
            foreach ($this->children as $child)
            {
                $ids[] = $child->id;
                if($child->children->count() >= 1)
                {
                    $ids = array_merge($ids, $child->getChildrenId());  
                }
            }   
        }

        return array_unique($ids);
    }

    public function missingchild(){
        return $this->belongsTo('Max\Missingchild\Models\Missingchild');
    }

    public function isRoot()
    {
        return $this->parent_id == null;
    }

    public function getRootId()
    {
        $parentId = $this->parent_id;

        if(!is_null($parentId) && $currentParent = static::find($parentId) )
        {
            return $currentParent->getRootId();
        }

        return $this->id;
    }
}