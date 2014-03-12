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
		return User::find($id);
	}

	public function findMany($ids)
	{
		return User::findMany($ids);
	}
}