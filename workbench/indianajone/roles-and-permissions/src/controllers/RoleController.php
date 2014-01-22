<?php namespace Indianajone\RolesAndsPermissions\Controllers;

use \BaseController;
use \Input;
use \Response;
use \Validator;
use \Indianajone\RolesAndPermissions\Role;

class RoleController extends BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		// Wait for respone helper.
		$offset = Input::get('offset', 0);
		$limit= Input::get('limit', 10);
		$field = Input::get('fields', null);
		$fields = explode(',', $field);
 		$roles =  Role::with('permits')->offset($offset)->limit($limit)->get();
 		
 		if($field)
	 		$roles->each(function($role) use ($fields){
	 			$role->setVisible($fields);
	 		});
        
        return Response::json(
        	array(
        		'header' => array(
        			'code' => 200,
        			'message' => 'success'
        		),
        		'offset' => (int) $offset,
        		'limit' => (int) $limit,
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
		$rules = array(
			'name' => 'required|unique:roles'
		);

		$validator = Validator::make(Input::all(), $rules);

		if ($validator->passes()) {
			$role = new Role();
			$role->name = Input::get('name', null);
			if($role->save())
				return Response::json(array(
					'header'=> [
		        		'code'=> 200,
		        		'message'=> 'success'
		        	],
					'id'=> $role->id
				), 200); 
		} 

		return Response::json(array(
			'header'=> [
        		'code'=> 400,
        		'message'=> $validator->messages()->first()
        	]
		), 200); 
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
      	$field = Input::get('fields', null);
		$fields = explode(',', $field);
 		$role =  Role::find($id);

 		if($role)
		{
			if($fields[0] == '' || in_array('permits', $fields)) $role->permits;
	 		if($field) $role->setVisible($fields);

		 	return Response::json(
	        	array(
	        		'header' => array(
	        			'code' => 200,
	        			'message' => 'success'
	        		),
	        		'entry' => $role->toArray()
	        	), 200
	        );
		}
		
		return Response::json(
        	array(
        		'header' => array(
        			'code' => 204,
        			'message' => 'No user were found.'
        		)
        	), 200
        );	
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
		$role =  Role::find($id);

		if($role)
		{
			$result = $role->update(array(
				'name'=> Input::get('name', $role->name)
			));

			if($result)
			{
				return Response::json(
		        	array(
		        		'header' => array(
		        			'code' => 200,
		        			'message' => 'Updated role_id: '.$id.' success!'
		        		)
		        	), 200
		        );
			}
			
			return Response::json(
	        	array(
	        		'header' => array(
	        			'code' => 500,
	        			'message' => 'Internal Server Error.'
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