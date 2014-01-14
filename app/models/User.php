<?php

use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableInterface;

use Zizaco\Entrust\HasRole;

class User extends BaseModel implements UserInterface, RemindableInterface {
	use HasRole; // Add this trait to your user model
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'users';

	 protected $appends = array(
        'fullname' );

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = array('password');

    /**
     * Many-to-Many relations with Role
     */
    public function roles()
    {
        return $this->belongsToMany('Indianajone\RolesAndPermissions\Role', 'user_roles');
    }

    public function getFullnameAttribute()
    {
    	return $this->first_name .' '. $this->last_name;
    }

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

}