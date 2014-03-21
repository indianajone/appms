<?php namespace Max\User\Repository;

use Input, Validator;
use Max\User\Models\User;
use Illuminate\Support\Contracts\ArrayableInterface;

class DBUserRepository implements UserRepositoryInterface
{
	/**
	 * The message bag instance.
	 *
	 * @var \Illuminate\Support\MessageBag
	 */
	public $errors;

	/**
	 * Validate as defined rules in Model.
	 *
	 * @param 	string 	$action
	 * @param  	array 	$input
	 * @return 	string|boolean
	 *
	 */
	public function validate($action, $input=null)
	{
		$validator = Validator::make($input ?: Input::all(), User::$rules[$action]);

		if($validator->passes()) return true;

		$this->errors = $validator->messages()->first();
		
		return false;
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

	public function find($id)
	{
        return  User::apiFilter()->findOrFail($id);
	}

	public function findMany($ids, $columns=array('*'))
	{
		return User::findMany($ids, $columns);
	}

	public function findWith($id, $relations)
	{
		$user = User::apiFilter()->whereId($id)->with('apps', 'roles')->first()->fields();

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

	public function create($input)
	{
		return User::create($input);
	}

	public function update($id, $input)
	{
		$user = $this->find($id);
		return $user->update($input);
	}

	public function delete($id)
	{
		return User::findOrFail($id)->delete();
	}

	/**
	*
	# TODO: Need to Revise too many queries.
	*
	**/

	public function children($id)
	{
		$user = User::find($id);

		if($user)
		{
			return User::whereIn('id', $user->getChildrenId())->apiFilter();
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