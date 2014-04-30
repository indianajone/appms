<?php namespace Max\User\Models;

use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableInterface;
// use Zizaco\Entrust\HasRole;


class User extends \Eloquent implements UserInterface, RemindableInterface 
{   
    protected $rules = array(
        'index' => array(
            'token' => 'required|exists:users,remember_token'
        ),
        'show' => array(
            'token' => 'required|exists:users,remember_token',
            'id' => 'required|exists:users'
        ),
        'create' => array(
            'token' => 'required|exists:users,remember_token',
            'username'  => 'required|unique:users,username',
            'password'  => 'required',
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email|unique:users,email',
        ),
        'update' => array(
            'token' => 'required|exists:users,remember_token',
            'email' => 'email|unique:users',
            'id' => 'required|exists:users'
        ),
        'delete' => array(
            'token' => 'required|exists:users,remember_token',
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
            'token' => 'required|exists:users,remember_token',
            'username'    => 'required|exists:users,username',
            'password' => 'required',
            'new_password' => 'required'
        ),
        'manage_role' => array(
            'token' => 'required|exists:users,remember_token',
            'id' => 'required|exists:users',
            'action' => 'required',
            'role_id' => 'required|existsloop:roles,id'
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
    protected $hidden = array('password', 'parent_id', 'meta', 'remember_token', 'deleted_at');

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = array('id');

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = array('last_seen');


    /**
     * Indicates if the model should soft delete.
     *
     * @var bool
     */
    // protected $softDelete = true;

    use \BaseModel;
    
    // Roles and Permissions Helper.
    // use HasRole; 

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
     * Get the token value for the "remember me" session.
     *
     * @return string
     */
    public function getRememberToken()
    {
        return $this->remember_token;
    }

    /**
     * Set the token value for the "remember me" session.
     *
     * @param  string  $value
     * @return void
     */
    public function setRememberToken($value)
    {
        $this->remember_token = $value;
    }

    /**
     * Get the column name for the "remember me" token.
     *
     * @return string
     */
    public function getRememberTokenName()
    {
        return 'remember_token';
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

    /**
     * Validate password.
     *
     * @return boolean
     */
    public function checkPassword($password)
    {
        return \Hash::check($password, $this->getAuthPassword());
    }

    /**
     * Define a one-to-many with Application.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function apps(){
        return $this->hasMany('Indianajone\\Applications\\Application', 'user_id');
    }

     /**
     * Define a one-to-many with Application.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function meta(){
        return $this->hasMany('Max\\User\\Models\\Usermeta', 'user_id');
    }

    /**
     * Define a many-to-many Role.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function roles()
    {
        return $this->belongsToMany('Indianajone\RolesAndPermissions\Role', 'user_roles');
    }

    /**
     * Define an inverse one-to-one or many User.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function parent()
    {
        return $this->belongsTo('Max\User\Models\User', 'parent_id');
    }

    /**
     * Define an inverse one-to-one or many with MissingChild.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function missingchild(){
        return $this->belongsTo('Max\Missingchild\Models\Missingchild');
    }

    /**
     * Define a one-to-many with User.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function children()
    {
        return $this->hasMany('Max\User\Models\User', 'parent_id');
    }

    /**
     * List all children ids.
     *
     * @param   array   $ids
     * @return  array 
     */
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

     /**
     * Check if current user is in top level.
     *
     * @return  boolean 
     */
    public function isRoot()
    {
        return $this->parent_id == null;
    }

    /**
     * Get top level user id.
     *
     * @return  int 
     */
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