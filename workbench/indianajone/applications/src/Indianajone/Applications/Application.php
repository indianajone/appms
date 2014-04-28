<?php namespace Indianajone\Applications;

class Application extends \Eloquent
{
	/**
	* The database table used by the model.
	*
	* @var string
	**/
	protected $table = 'applications';
	protected $fillable = array('name', 'description', 'user_id', 'appkey');
	/**
	* The attributes excluded from the model's JSON form.
	*
	* @var array
	**/
	protected $hidden = array('appkey', 'user_id', 'meta');

	protected $rules = array(
		'show' => array(
			'token' => 'required|exists:users,remember_token',
			'id' 		=> 'exists:applications',
		),
		'create' => array(
			'token' => 'required|exists:users,remember_token',
			'name'		=> 'required'
		),
		'update' => array(
			'token' => 'required|exists:users,remember_token',
			'id' 		=> 'required|exists:applications',
			'user_id' 		=> 'exists:users,id'
		),
		'delete' => array(
			'token' => 'required|exists:users,remember_token',
			'id' 		=> 'required|exists:applications'
		)
	);

	use \BaseModel;

	/**
	* Application Owner
	*
	* @return Max\User\Models\User
	**/
	public function owner()
	{
		return $this->belongsTo('Max\\User\\Models\\User', 'user_id');
	}

	/**
	* Application Metadata
	*
	* @return Indianajone\Applications\ApplicationMeta
	**/
	public function meta()
	{
		return $this->hasMany('Indianajone\\Applications\\ApplicationMeta', 'app_id');
	}

	public function genKey()
	{
		return str_random(32);
	}

	public function getAppIDByKey($key)
	{
		return $this->whereAppkey($key)->first();
	}

	public function scopeSearch($query)
    {
        return $this->keywords(array('name'));
    }
}