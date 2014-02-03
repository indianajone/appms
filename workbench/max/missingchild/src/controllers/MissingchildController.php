<?php namespace Max\Missingchild\Controllers;

use Validator, Input, Response, Hash, Appl;
use Max\Missingchild\Models\Missingchild as Child;
use Carbon\Carbon;


class MissingchildController extends \BaseController {

	public function index()
	{
		# code...
	}

	public function create()
	{
		return $this->store();
	}

	public function store()
	{
		$validator = Validator::make(Input::all(), Child::$rules['create']);

 		if($validator->passes())
 		{
 			return 'yeah';
 			// $child = Child::create(array(
 			// 	'title' => Input::get('title'),
 			// 	'first_name' => Input::get('first_name'),
 			// 	'last_name' => Input::get('last_name'),
 			// 	'nickname' => Input::get('nickname'),
 			// 	'gender' => Input::get('gender'),
 			// 	'lost_age' => Input::get('lost_age'),
 			// 	'latitude' => Input::get('latitude'),
 			// 	'longitude' => Input::get('longitude'),
 			// 	'note' => Input::get('note'),
 			// 	'order' => Input::get('order'),
 			// ));
 		}

 		return Response::message(400, $validator->messages()->first());
	}

	public function show($id)
	{
		# code...
	}

	public function edit($id)
	{
		return $this->update($id);
	}

	public function update($id)
	{
		# code...
	}

	public function delete($id)
	{
		return $this->destroy($id);
	}

	public function destroy($id)
	{
		# code...
	}

}