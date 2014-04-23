<?php namespace Indianajone\RolesAndsPermissions\Controllers;

use Appl, BaseController, Input, Response, Validator;
use Indianajone\RolesAndPermissions\Role;
use Indianajone\RolesAndPermissions\RoleRepositoryInterface;

class ApiRoleController extends BaseController {

	public function __construct(RoleRepositoryInterface $roles)
	{
		parent::__construct();
		$this->roles = $roles;
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$roles = $this->roles->all();
        
      return Response::result(
        	array(
        		'header' => array(
        			'code' => 200,
        			'message' => 'success'
        		),
        		'offset' => (int) Input::get('offset'),
        		'limit' => (int) Input::get('limit'),
        		'total' => $roles->count(),
        		'entries' => $roles->toArray()
        	)
      );
	}

	/**
	 * Store a newly created resource in storage with GET.
	 *
	 * @return Response
	 */
	public function create()
	{
        return $this->store();
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		if ($this->roles->validate('create')) {
			$role = $this->roles->create(array(
				'app_id' => Appl::getAppIDByKey(Input::get('appkey')),
				'name' => Input::get('name')
			));

			if($role->save())
				return Response::result(array(
	                    'header' => array(
	                        'code'      => 200,
	                        'message'   => 'success'
	                    ),
	                    'id' => $role->id
	                ));

			return Response::message(500, 'Something wrong when trying to create role.');
		} 

		return Response::message(400, $this->roles->errors());
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		$validator = $this->roles->validate('show', array('id'=>$id));

		if($validator)
		{
			$role = $this->roles->find($id);

			return Response::result(array(
				'header' => array(
        			'code' => 200,
        			'message' => 'success'
        		),
        		'entry' => compact('role')
			));
		}

		return Response::message(400, $this->roles->errors());
	}

	/**
	 * Update the specified resource in storage with GET.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		return $this->update($id);
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		$input = array_add(Input::all(), 'id', $id);
		if($this->roles->validate('show', $input))
		{
			$input = Input::only('name');
			$role = $this->roles->update($id, $input);

         if($role)
				return Response::message(200, 'Updated role id: '.$id.' success!'); 

			return Response::message(500, 'Something wrong when trying to update role.');
		}

		return Response::message(400, $this->roles->errors());
	}

	/**
	 * Remove the specified resource from storage with POST.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function delete($id)
	{
		return $this->destroy($id);
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		$role = $this->roles->find($id)->delete();
	
		return Response::json(
        	array(
        		'header' => array(
        			'code' => 200,
        			'message' => 'Deleted role_id: '.$id.' success!'
        		)
        	), 200
        );
	}

	/**
	 * Attach the permissions to specified roles.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function attachPermissions($id)
	{
		if($this->roles->validate('attach'))
		{
			$ids = Input::get('permission_id');
			$role = $this->roles->syncPermissions($id, array_map('intval', explode(',', $ids)));
			return Response::message(200, 'permission_id: '. $ids .' is attached to ' . $role->name);
		}

		return Response::message(400, $this->roles->errors());
	}
}