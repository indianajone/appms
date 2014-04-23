<?php
namespace Max\Member\Models;

use Illuminate\Auth\UserInterface;


class Member extends \Eloquent implements UserInterface{
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
    		'email' => 'required|email|uniqueinapp:members,email',
    		'type' => 'required'
    	),
        'update' => array(
            'appkey'   => 'required|exists:applications,appkey',
            'id' => 'required|exists:members',
            'email' => 'required|email|exists:members,email'
        ),
        'delete' => array(
            'appkey'   => 'required|exists:applications,appkey',
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

    use \BaseModel;

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

}