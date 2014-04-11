<?php namespace Max\User\Models;

use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableInterface;
use Zizaco\Entrust\HasRole;


class User extends \Eloquent implements UserInterface, RemindableInterface 
{
    use \BaseModel;

    protected $rules = array(
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
            // 'email' => 'required|email|unique:users'
            'id' => 'required|exists:users'
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

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = array('id');

    
    // Roles and Permissions Helper.
    use HasRole; 

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