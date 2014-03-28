<?php namespace Indianajone\RolesAndPermissions;

use Input, Validator;
 
class DBRoleRepository implements RoleRepositoryInterface
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
		$validator = Validator::make($input ?: Input::all(), Role::$rules[$action]);

		if($validator->passes()) return true;

		$this->errors = $validator->messages()->first();
		
		return false;
	}

	public function all()
	{
		$roles = Role::apiFilter()->get();

		$roles->each(function($role){
			$role->fields();
		});

		if($roles instanceof ArrayableInterface)
        	return $roles->toArray();

		return $roles;
	}

	public function find($id)
	{
		return Role::apiFilter()->with('permits')->findOrFail($id)->fields();
	}


	public function create($input)
	{
		return Role::create($input);
	}

	public function update($id, $input)
	{
		return Role::whereId($id)->update($input);
	}

	public function delete($id)
	{
		return $this->find($id)->delete();
		
	}
}