<?php namespace Max\User\Repository;

use Appl, Input, Validator;
use Max\User\Models\User;
use Max\User\Models\Usermeta;
use Illuminate\Support\Contracts\ArrayableInterface;

class DBUserRepository extends \AbstractRepository implements UserRepositoryInterface
{
	public function __construct(User $user, Usermeta $meta)
	{
		parent::__construct($user);
		$this->meta = $meta;
	}

	public function all()
	{
		$users =$this->model->apiFilter()->with('meta')->get();

		$users->each(function($user){
			$user->getMeta()->fields();
		});

		if($users instanceof ArrayableInterface)
			return $users->toArray();

       return $users;
	}

	public function create($input)
	{
		$id = parent::create($input);

		if($id)
		{
			$this->meta->create(array(
				'user_id' => (int) $id,
				'meta_key' => 'last_seen'
			));
		}

		return $id;
	}

	public function updateMeta($id, $attributes=array())
	{
		$user = $this->model->with('meta')->find($id);
		$meta = $user->meta()->first();

		foreach($attributes as $key => $value)
		{
			$meta->fill(array(
				'meta_key' => $key,
				'meta_value' => $value
			));
		}

		return $meta->save();
	}

	public function findMany($ids, $columns=array('*'))
	{
		return $this->model->findMany($ids, $columns);
	}

	public function findWith($id, $relations=array())
	{
		$user = $this->model->apiFilter()->whereId($id)->with($relations)->firstOrFail()->getMeta()->fields();

    	foreach($relations as $relation)
    	{
    		$user->{$relation}->each(function($model)
    		{
    			$model->setVisible(array(
    				'id', 'name'
    			));
    		});
    	}

		if($user instanceof ArrayableInterface)
        	return $user->toArray();

      return $user;
	}

	public function findUserAndChildren($id)
	{
		$user = $this->children($id);

		if($user)
		{
			$users = $user->apiFilter()->get();
			$users->each(function($user){
            	$user->getMeta()->fields();
        	});

        	if($users instanceof ArrayableInterface)
        		return $users->toArray();

        	return $users;
		}

		return false;
	}

	/**
	*
	# TODO: Need to Revise too many queries.
	*
	**/

	public function children($id)
	{
		$user = $this->model->findOrFail($id);

		if($user)
		{
			return $this->model->whereIn('id', $user->getChildrenId());
		}

		return null;
	}
	
	public function countChildren($id)
	{
		$user = $this->children($id);

		if($user)
		{
			return $user->count();
		}

		return 0;
	}

	public function delete($id)
	{
		$user = $this->model->find($id);
		
		$user->meta()->delete();

		return $user->delete();
	}

	public function getIDByToken($token)
	{
		return !is_null($token) ? $this->model->where($this->model->getRememberTokenName(), '=', $token)->firstOrFail()->getAuthIdentifier() : null;
	}
}