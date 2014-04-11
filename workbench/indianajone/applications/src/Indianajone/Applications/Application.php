<?php namespace Indianajone\Applications;

class Application extends \Eloquent
{
	/**
	* The database table used by the model.
	*
	* @var string
	**/
	protected $table = 'applications';
	protected $fillable = array('name', 'description', 'picture', 'user_id', 'appkey');
	protected $perPage = 10;
	/**
	* The attributes excluded from the model's JSON form.
	*
	* @var array
	**/
	protected $hidden = array('appkey', 'user_id', 'meta');

	protected $rules = array(
		'show' => array(
			// 'appkey' => 'required',
			'id' 		=> 'exists:applications',
			'user_id' 	=> 'required|exists:users,id'
		),
		'create' => array(
			'user_id' 	=> 'required|exists:users,id',
			'name'		=> 'required'
		),
		'update' => array(
			'user_id' 	=> 'required|exists:users,id'
		),
		'delete' => array(
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

	/**
	* Get definded rules in Model.
	*
	* @return Array
	**/
	public function rules($action)
	{
		return $this->rules[$action];
	}

	public function genKey()
	{
		return str_random(32);
	}

	public function getAppIDByKey($key)
	{
		return $this->whereAppkey($key)->first();
	}
}