<?php namespace Indianajone\RolesAndPermissions;
 
interface RoleRepositoryInterface extends \AbstractRepositoryInterface
{
	public function syncPermissions($id, $permissions=array());
}