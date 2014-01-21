<?php namespace Indianajone\RolesAndsPermissions\Controllers;

use \BaseController;
use \Input;
use \Response;
use \Validator;
use \Indianajone\RolesAndPermissions\Permission;

class PermissionController extends BaseController {

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
        
        return Response::json(
        	array(
        		'header' => array(
        			'code' => 200,
        			'message' => 'success'
        		),
        		'offset' => (int) $offset,
        		'limit' => (int) $limit,
        		'total' => $perms->count(),
        		'entries' => $perms->toArray()
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
			'name' => 'required',
			'display_name' => 'required'
		);

		$validator = Validator::make(Input::all(), $rules);

		if ($validator->fails()) {
			return Response::json(array(
				'header'=> [
	        		'code'=> 400,
	        		'message'=> $validator->messages()->first()
	        	]
			), 200); 
		} else {
			$p = Permission::create(Input::all());
			if($p)
				return Response::json(array(
					'header'=> [
		        		'code'=> 200,
		        		'message'=> 'success'
		        	],
					'id'=> $p->id
				), 200); 
		} 
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

		 	return Response::json(
	        	array(
	        		'header' => array(
	        			'code' => 200,
	        			'message' => 'success'
	        		),
	        		'entry' => $perm->toArray()
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

			if($result)
			{
				return Response::json(
		        	array(
		        		'header' => array(
		        			'code' => 200,
		        			'message' => 'Updated permission_id: '.$id.' success!'
		        		)
		        	), 200
		        );
			}
			else
			{
				return Response::json(
		        	array(
		        		'header' => array(
		        			'code' => 500,
		        			'message' => 'Internal Server Error.'
		        		)
		        	), 200
		        );
			}
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
		$perm = User::find($id);
		if($perm) 
		{
			$perm->delete();
			return Response::json(
	        	array(
	        		'header' => array(
	        			'code' => 200,
	        			'message' => 'Deleted permission_id: '.$id.' success!'
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
}