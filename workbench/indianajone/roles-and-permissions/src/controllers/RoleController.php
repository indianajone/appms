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

 		// if(in_array(array('permit', 'permits', '*'), $fields)) $roles->with('permits');
 	// 	->each(function($role) {
		// 	$f = explode(',', Input::get('fields', '*'));
		// 	// $role->permits;
		// 	// $role->setHidden($f);
			// if(!$fields || !in_array('*', $fields))
			// {
				var_dump($fields);
		// 		list($keys, $values) = array_divide($f);
		// 		$hide = array_except($role->attributesToArray(), $values);
		// 		list($keys, $values) = array_divide($hide);
		// 		$role->setHidden($keys);
			// }
		// });



        
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
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
        
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

		if ($validator->fails()) {
			return Response::json(array(
				'header'=> [
	        		'code'=> 400,
	        		'message'=> $validator->messages()->first()
	        	]
			), 200); 
			//return Response::missing($validator->messages()->first());
		} else {
			$role = new Role();
			$role->name = Input::get('name', null);

			$result = $role->save();

			return Response::json(array(
				'header'=> [
	        		'code'=> 200,
	        		'message'=> 'success'
	        	],
				'id'=> $role->id
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
       
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
        
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		//
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		//
	}

}