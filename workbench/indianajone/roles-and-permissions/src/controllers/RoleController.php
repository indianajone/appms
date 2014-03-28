<?php namespace Indianajone\RolesAndsPermissions\Controllers;

use \BaseController;
use \Input;
use \Response;
use \Validator;
use \Indianajone\RolesAndPermissions\Role;
use \Indianajone\RolesAndPermissions\RoleRepositoryInterface;

class RoleController extends BaseController {

	public function __construct(RoleRepositoryInterface $roles)
	{
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
		$validator = $this->roles->validate('create');

		if ($validator) {
			$role = $this->roles->create(array(
				'name' => Input::get('name')
			));

			if($role)
				return Response::result(array(
	                    'header' => array(
	                        'code'      => 200,
	                        'message'   => 'success'
	                    ),
	                    'id' => $role->id
	                ));

			return Response::message(500, 'Something wrong when trying to create role.');
		} 

		return Response::message(400, $this->roles->errors);
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
        		'entry' => $role->toArray()
			));
		}

		return Response::message(400, $this->roles->errors);
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
		// $validator = $this->roles->validate('show', array('id'=>$id));

		// if($validator)
		// {
			$inputs = Input::only('name');
			$role = $this->roles->find($id);

            foreach ($inputs as $key => $val) {
                if( $val == null || 
                    $val == '' || 
                    $val == $role[$key]) 
                {
                    unset($inputs[$key]);
                }
            }

            if(!count($inputs))
                return Response::message(204, 'Nothing is update.');

            if($role->update($inputs))
                return Response::message(200, 'Updated role id: '.$id.' success!'); 

            return Response::message(500, 'Something wrong when trying to update role.');
		// }

		// return Response::message(400, $this->roles->errors);
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
		$role = Role::find($id);
		if($role) 
		{
			$role->delete();
			return Response::json(
	        	array(
	        		'header' => array(
	        			'code' => 200,
	        			'message' => 'Deleted role_id: '.$id.' success!'
	        		)
	        	), 200
	        );
		}
		else
		{
			return Response::json(
	        	array(
	        		'header' => array(
	        			'code' => 204,
	        			'message' => 'No user were found.'
	        		)
	        	), 200
	        );
		}
	}

	/**
	 * Attach the permissions to specified roles.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function attachPermissions($id)
	{
		/**
			#TODO: Move Validation to service and Rules to Model.
		**/
		$rules = array(
			'permission_id' => 'required|existloop:permissions,id'
		);

		/**
			#TODO: Find a better place for resolver.
		**/
		Validator::resolver(function($translator, $data, $rules, $messages)
		{
		    return new \Indianajone\Validators\Rules\ExistLoop($translator, $data, $rules, $messages);
		});

		$validator = Validator::make(Input::all(), $rules);

		if($validator->passes())
		{
			$role = Role::find($id);
			if(!$role) 
				return Response::json(array(
					'header'=> [
		        		'code'=> 400,
		        		'message'=> 'Role id: '. $id .' can not be found'
		        	]
				), 200);
			else 
			{
				$ids = Input::get('permission_id');
				$role->permits()->sync(array_map('intval', explode(',', $ids)));
				return Response::json(array(
					'header'=> [
		        		'code'=> 200,
		        		'message'=> 'permission_id: '. $ids .' is attached to ' . $role->name
		        	]
				), 200);
			}
		}

		return Response::json(array(
			'header'=> [
        		'code'=> 204,
        		'message'=> $validator->messages()->first()
        	]
		), 200); 
	}
}