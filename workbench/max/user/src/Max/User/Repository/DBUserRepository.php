<?php namespace Max\User\Repository;

use Appl, Input, Validator;
use Max\User\Models\User;
use Illuminate\Support\Contracts\ArrayableInterface;

class DBUserRepository extends \AbstractRepository implements UserRepositoryInterface
{
	public function __construct(User $user)
	{
		parent::__construct($user);
	}

	public function all()
	{
		$users =$this->model->apiFilter()->get();

		$users->each(function($user){
			$user->fields();
		});

		if($users instanceof ArrayableInterface)
			return $users->toArray();

       return $users;
	}

	public function findMany($ids, $columns=array('*'))
	{
		return $this->model->findMany($ids, $columns);
	}

	public function findWith($id, $relations)
	{
		$user = $this->model->apiFilter()->whereId($id)->with($relations)->firstOrFail()->fields();

		$user->roles->each(function($role){
    		$role->setVisible(array('id','name'));
    	});

    	$user->apps->each(function($app){
    		$app->setVisible(array('id','name'));
    	});

		if($user instanceof ArrayableInterface)
        	return $user->toArray();

        return $user;
	}

	public function findUserAndChildren($id)
	{
		$app = Appl::getAppByKey(Input::get('appkey'));
		
		if($app)
		{
			$user = $this->model->findOrFail($id);
			$owner = $app->user_id;
			if(!in_array($owner, $user->getChildrenId()))
			return false;
		}
		

		$user = $this->children($id);

		if($user)
		{
			$users = $user->with('roles')->apiFilter()->get();
			$users->each(function($user){
            	$user->fields();
            	$user->roles->each(function($role){
            		$role->setVisible(array('id','name'));
            	});
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
}