<?php namespace Indianajone\RolesAndPermissions;

use Input, Validator;
 
class DBRoleRepository extends \AbstractRepository implements RoleRepositoryInterface
{
	public function __construct(Role $roles)
	{
		parent::__construct($roles);
	}

	public function all()
	{
		$roles = $this->model->apiFilter()->get();

		$roles->each(function($role){
			$role->fields();
		});

		if($roles instanceof ArrayableInterface)
        	return $roles->toArray();

		return $roles;
	}

	public function find($id)
	{
		return $this->model->apiFilter()->with('permits')->findOrFail($id)->fields();
	}
}