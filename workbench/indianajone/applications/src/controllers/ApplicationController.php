<?php namespace Indianajone\Applications\Controllers;

use \BaseController;
use \Input;
use \Response;
use \Validator;
use \Indianajone\Applications\Application as Appl;
use \Image;

class ApplicationController extends BaseController
{
	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$offset = Input::get('offset', 0);
		$limit= Input::get('limit', 10);
		$field = Input::get('fields', null);
		$fields = explode(',', $field);
		$apps = Appl::offset($offset)->limit($limit)->get();

		if($field)
	 		$apps->each(function($app) use ($fields){
	 			$app->setVisible($fields);
	 		});

	 	return Response::json(
        	array(
        		'header' => array(
        			'code' => 200,
        			'message' => 'success'
        		),
        		'offset' => (int) $offset,
        		'limit' => (int) $limit,
        		'total' => $apps->count(),
        		'entries' => $apps->count() >= 1 ? $apps->toArray() : null
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
			'user_id' 	=> 'required|exists:users,id',
			'name'		=> 'required'
		);

		$messages = array(
			'exists' => 'The given :attribute does not exists'
		);

		$validator = Validator::make(Input::all(), $rules, $messages);
		if ($validator->passes()) {

			$app = new Appl();
			$app->name = Input::get('name');
			$app->user_id = Input::get('user_id');
			$app->description = Input::get('description', '');
			$app->appkey = str_random(32);
			$picture = Input::get('picture', null);
			if($picture)
			{
				$response = Image::upload($picture);
				if(is_object($response)) return $response;
				$app->picture = $response;
			}

			if($app->save())
				return Response::json(array(
					'header'=> [
		        		'code'=> 200,
		        		'message'=> 'success'
		        	],
					'id'=> $app->id
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
		$app = Appl::with('owner')->find($id);

		if($app)
			return Response::json(
				array(
	        		'header' => array(
	        			'code' => 200,
	        			'message' => 'success'
	        		),
	        		'entry' => $app->toArray()
	        	), 200
			);

		return Response::json(
        	array(
        		'header' => array(
        			'code' => 204,
        			'message' => 'Application id: '. $id .' does not exists.'
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
		$validator = Validator::make(Input::all(), Appl::$rules['update']);

		if ($validator->passes()) {
			$app = Appl::find($id);
			$app->name = Input::get('name', $app->name);
			$app->description = Input::get('description', $app->description);
			$app->picture = Input::get('picture', null);
			if($app->save())
				return Response::json(
		        	array(
		        		'header' => array(
		        			'code' => 200,
		        			'message' => 'Updated app_id: '.$id.' success!'
		        		)
		        	), 200
		        );
		}

		return Response::json(array(
			'header'=> [
        		'code'=> 400,
        		'message'=> $validator->messages()->first()
        	]
		), 200); 
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
		$validator = Validator::make(array( 'id' => $id), Appl::$rules['delete']);

		if ($validator->passes()) {
			$app->find($id)->delete();
			return Response::json(
	        	array(
	        		'header' => array(
	        			'code' => 200,
	        			'message' => 'Deleted Application: '.$id.' success!'
	        		)
	        	), 200
	        );
		}

		return Response::json(array(
			'header'=> [
        		'code'=> 400,
        		'message'=> $validator->messages()->first()
        	]
		), 200); 
	}
}