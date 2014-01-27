<?php
namespace Max\Member\Models;

use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableInterface;
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
    protected $hidden = array('app_id', 'password');

    public static $rules = array();

	public function apps(){
		return $this->hasMany('Indianajone\Applications\Application', 'appkey');
	}

}