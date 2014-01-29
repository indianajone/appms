<?php
namespace Max\Member\Models;

use Illuminate\Auth\UserInterface;
use \BaseModel;

class Member extends BaseModel implements UserInterface{
	/**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'members';

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = array('app_id', 'password', 'status');
    protected $guarded = array('id');

    public static $rules = array(
        'show' => array(
            'appkey'    => 'required|exists:applications,appkey',
        ),
    	'create' => array(
            'appkey'    => 'required|exists:applications,appkey',
    		'username' => 'required|unique:members,username',
    		'password'  => 'required',
    		'confirm_password' => 'required|same:password',
    		'first_name' => 'required',
    		'last_name' => 'required',
    		'email' => 'required|email|unique:members,email',
    		'type' => 'required'
    	),
        'update' => array(
            'id' => 'required|exists:members',
            'email' => 'required|email|exists:members,email'
        ),
        'delete' => array(
            'id' => 'required|exists:members'
        ),
        'login' => array(
            'username'  => 'required|exists:members,username',
            'password'  => 'required'
        ),
        'resetPwd' => array(
            'username'    => 'required|exists:members,username',
            'password' => 'required',
            'new_password' => 'required'
        ),
    );

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

    public function checkPassword($password)
    {
        return \Hash::check($password, $this->getAuthPassword());
    }

}