<?php namespace Indianajone\RolesAndsPermissions\Controllers;

use \BaseController;
use \Input;
use \Response;
use \Validator;
use \Indianajone\RolesAndPermissions\Permission;
use \Max\User\Models\User;

class ApiPermissionController extends BaseController {

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
 		$perms =  Permission::offset($offset)->limit($limit)->get();
 		
 		if($field)
	 		$perms->each(function($perm) use ($fields){
	 			$perm->setVisible($fields);
	 		});
        
        return Response::listing(
        	array(
        		'code' => 200,
        		'message' => 'success'
        	), $perms, $offset, $limit
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
            'name'          => 'required',
            'display_name'  => 'required'
        );
		/**
		 #TODO Need to look for Ardent Fixed issue.
		*/ 
		$validator = Validator::make(Input::all(), $rules);
		if($validator->passes())
		{
			$perm = new Permission();
			$perm->name = Input::get('name');
			$perm->display_name = Input::get('display_name');
			$perm->save();
			// if($perm->save())
				return Response::result(array(
					'header'=> array(
		        		'code'=> 200,
		        		'message'=> 'success'
		        	), 'id'=> $perm->id
				));
		} 

		return Response::message(400, $validator->messages()->first()); 
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
 		$perm =  Permission::find($id);

 		if($perm)
		{
			if($fields[0] == '' || in_array('permits', $fields)) $perm->permits;
	 		if($field) $perm->setVisible($fields);

		 	return Response::result(
	        	array(
	        		'header' => array(
	        			'code' => 200,
	        			'message' => 'success'
	        		),
	        		'entry' => $perm->toArray()
	        	)
	        );
		}
		return Response::message(204, 'Permission id: '. $id .' does not exists.');
 		
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
		$perm =  Permission::find($id);

		if($perm)
		{
			$result = $perm->update(array(
				'name'=> Input::get('name', $perm->name)
			));

			if($result) return Response::message(200, 'Updated permission_id: '.$id.' success!');
			
		}
		
		return Response::message(204, 'Permission id: '. $id .' does not exists.');
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
		$perm = User::find($id);
		if($perm) 
		{
			$perm->delete();
			return Response::message(200, 'Deleted permission_id: '.$id.' success!');
		}
		
		return Response::message(204, 'Permission id: '. $id .' does not exists.');
	}
}