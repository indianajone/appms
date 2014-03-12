<?php namespace Max\User\Repository;

use Max\User\Models\User;

class DBUserRepository implements UserRepositoryInterface
{
	public function all()
	{
		return User::all();
	}

	public function find($id)
	{
		return User::findOrFail($id);
	}

	public function findMany($ids)
	{
		return User::findMany($ids);
	}

	public function findUserAndChildren($id)
	{
		$user = User::find($id);

		if($user)
		{
			$users = User::whereIn('id', $user->getChildrenId())->with('roles')->apiFilter()->get();
			$users->each(function($user){
            	$user->fields();
        	});

        	return $users;
		}

		return false;
	}
}