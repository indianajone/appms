<?php namespace Indianajone\RolesAndPermissions;
 
class DBRoleRepository implements RoleRepositoryInterface
{
	public function validate($action, $input=null)
	{

	}

	public function all()
	{
		return Role::all();
	}

	public function find($id)
	{
		return Role::find($id);
	}


	public function create($input)
	{

	}

	public function update($id, $input)
	{

	}

	public function delete($id)
	{

	}
}