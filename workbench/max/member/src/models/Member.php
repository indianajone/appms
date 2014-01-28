<?php
namespace Max\Member\Models;

use \BaseModel;

class Member extends BaseModel {
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
    	'create' => array(
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
        )
    );

    public function scopeActive($query)
    {
        return $query->whereStatus(1);
    }

}