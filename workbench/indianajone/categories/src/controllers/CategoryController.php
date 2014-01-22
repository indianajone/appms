<?php namespace Indianajone\Categories\Controllers;

use \BaseController;
use \Input;
use \Response;
use \Validator;
use \Indianajone\Applications\Models\Application as Appl;
use \Indianajone\Categories\Category;

class CategoryController extends BaseController
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
		$cats = Category::with('children')->offset($offset)->limit($limit)->get();

		if($field)
	 		$cats->each(function($cat) use ($fields){
	 			$cat->setVisible($fields);
	 		});

	 	return Response::json(
        	array(
        		'header' => array(
        			'code' => 200,
        			'message' => 'success'
        		),
        		'offset' => (int) $offset,
        		'limit' => (int) $limit,
        		'total' => $cats->count(),
        		'entries' => $cats->count() >= 1 ? $cats->toArray() : null
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
		// $rules = array(
		// 	'user_id' 	=> 'required|exists:users,id',
		// 	'name'		=> 'required'
		// );

		// $messages = array(
		// 	'exists' => 'The given :attribute does not exists'
		// );

		$validator = Validator::make(Input::all(), Category::$rules['save']);

		// $validator = Validator::make(Input::all(), $rules, $messages);
		if ($validator->passes()) {
			$cat = Category::create(array(
				'name' => Input::get('name'),
				// 'app_id' => Appl::getAppID()
				'app_id' => 1,
				'parent_id' => Input::get('parent_id')
			));
		
		// 	$picture = Input::get('picture', null);
		// 	if($picture)
		// 	{
		// 		$response = Image::upload($picture);
		// 		if(is_object($response)) return $response;
		// 		$app->picture = $response;
			// }

		// 	if($app->save())
			return Response::json(array(
				'header'=> [
	        		'code'=> 200,
	        		'message'=> 'success'
	        	],
				'id'=> $cat->id
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
		$cat = Category::with('children')->find($id);

		if($cat)
			return Response::json(
				array(
	        		'header' => array(
	        			'code' => 200,
	        			'message' => 'success'
	        		),
	        		'entry' => $cat->toArray()
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
		/**
			#TODO: Find a better place for resolver.
		**/
		Validator::resolver(function($translator, $data, $rules, $messages)
		{
		    return new \Indianajone\Validators\Rules\ExistsOrNull($translator, $data, $rules, $messages);
		});

		$validator = Validator::make(Input::all(), Category::$rules['update']);

		if ($validator->passes()) {
			$cat = Category::find($id);
			$cat->name = Input::get('name', $cat->name);
			$cat->description = Input::get('description', $cat->description);
			$parent_id = Input::get('parent_id', $cat->parent_id, null);

			if(!is_null($parent_id))
			{
				$cat->updateParent($parent_id);
			}

			if($cat->save())
				return Response::json(
		        	array(
		        		'header' => array(
		        			'code' => 200,
		        			'message' => 'Updated category_id: '.$id.' success!'
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
	        			'message' => 'Deleted category_'.$id.' success!'
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