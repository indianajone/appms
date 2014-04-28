<?php namespace Indianajone\Applications;

use Auth, Config, Input;
use Indianajone\Applications\Application;
use Indianajone\Applications\ApplicationMeta;
use Illuminate\Support\Contracts\ArrayableInterface;
use Max\User\Repository\UserRepositoryInterface;

class DBAppRepository extends \AbstractRepository Implements AppRepositoryInterface
{

	protected $countWithChildren;

	public function __construct(
		Application $apps, 
		ApplicationMeta $meta,
		UserRepositoryInterface $users)
	{
		parent::__construct($apps);
		$this->meta = $meta;
		$this->users = $users;
	}

	public function countWithChildren()
	{
		return $this->countWithChildren;
	}

	public function all()
	{
		$id = $this->users->getIDByToken(Auth::user() ? Auth::user()->getRememberToken() : Input::get('token'));
		$users = $this->users->children($id);
		$query = $this->model->whereIn('user_id', $users->lists('id'));
		$this->countWithChildren = $query->count();
		$apps = $query->apiFilter()->with('meta')->get();

		$apps->each(function($app)
		{
			$app->getMeta()->fields();
		});

		return $apps->toArray();
	}

	public function find($id)
	{
		$user_id = $this->users->getIDByToken(Input::get('token'));
		$users = $this->users->children($user_id);

		$app = $this->model->apiFilter()->with('meta')->findOrFail($id);
		$app->getMeta()->fields();

		if(in_array($app->user_id, $users->lists('id')))
			return $app->toArray();
		else 
			return null;
	}

	public function findByKey($key)
	{
		return $this->model->whereAppkey($key)->first();
	}

	
	public function create($input)
	{
		$uid = $this->users->getIDByToken(Input::get('token'));
		$input = array_add($input, 'user_id', $uid);
		$id = parent::create($input);

		/**
		  #TODO: Move to Meta Object later on.
		 */

		$default = Config::get('applications::meta');
		$json = Input::get('meta');

		if($json)
		{
			$meta = json_decode($json, true);

			if( !is_null($meta) )
			{
				foreach( $meta as $key => $value )
				{
					if( !$this->meta->is_protected($key) )
						$default = array_key_exists($key, $default) ? 
							array_set($default, $key, $value) : 
							array_add($default, $key, $value);
				}
			}
		}

		if($id && $default)
		{
			foreach($default as $key => $value )
			{
				$this->meta->create(array(
					'app_id' => (int) $id,
					'meta_key' => $key,
					'meta_value' => $value
				));
			}
		}

		return $id;
	}

	public function update($id, $input)
	{
		$user_id = $this->users->getIDByToken(Input::get('token'));
		$users = $this->users->children($user_id);
		$app = $this->model->find($id);

		if(!in_array($app->user_id, $users->lists('id')))
		{
			return false;
		}

		return parent::update($id, $input);
	}

	public function updateMeta($id, $attributes=array())
	{
		$app = $this->model->with('meta')->find($id);
		$meta = $app->meta()->first();

		foreach($attributes as $key => $value)
		{
			$meta->fill(array(
				'meta_key' => $key,
				'meta_value' => $value
			));
		}

		return $meta->save();
	}

	public function delete($id)
	{
		$app = $this->model->find($id);
		
		$app->meta()->delete();

		return $app->delete();
	}
}