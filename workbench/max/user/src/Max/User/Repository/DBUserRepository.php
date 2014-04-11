<?php namespace Max\User\Repository;

use Input, Validator;
use Max\User\Models\User;
use Illuminate\Support\Contracts\ArrayableInterface;

class DBUserRepository extends \AbstractRepository implements UserRepositoryInterface
{
	public function __construct(User $user)
	{
		$this->model = $user;
	}

	public function all()
	{
        $users = User::apiFilter()->get();

        $users->each(function($user){
        	$user->fields();
        });

        if($users instanceof ArrayableInterface)
        	return $users->toArray();

        return $users;
	}

	public function findMany($ids, $columns=array('*'))
	{
		return User::findMany($ids, $columns);
	}

	public function findWith($id, $relations)
	{
		$user = User::apiFilter()->whereId($id)->with($relations)->firstOrFail()->fields();

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
		$user = User::findOrFail($id);

		if($user)
		{
			return User::whereIn('id', $user->getChildrenId());
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