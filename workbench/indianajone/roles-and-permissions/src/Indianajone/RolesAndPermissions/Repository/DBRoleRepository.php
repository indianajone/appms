<?php namespace Indianajone\RolesAndPermissions;
 
class DBRoleRepository extends \AbstractRepository implements RoleRepositoryInterface
{
	public function __construct(Role $roles)
	{
		parent::__construct($roles);
	}

	public function all()
	{
		$roles = $this->model->with('permits')->apiFilter()->get();

		$roles->each(function($role){
			$role->fields();
		});

		return $roles;
	}

	public function find($id)
	{
		return $this->model->apiFilter()->with('permits')->findOrFail($id)->fields();
	}

	public function syncPermissions($id, $ids=array())
	{
		$role = $this->model->findOrFail($id);
		$role->permits()->sync($ids);

		return $role;
	}
}