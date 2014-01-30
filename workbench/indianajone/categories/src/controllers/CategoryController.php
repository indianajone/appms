<?php namespace Indianajone\Categories\Controllers;

use \BaseController;
use \Input;
use \Response;
use \Validator;
use \Appl;
use Carbon\Carbon;
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
	
		$updated_at = Input::get('updated_at', null);
		$created_at = Input::get('created_at', null);

		$cats = Category::with('children');
		if($updated_at || $created_at)
		{
			if($updated_at) $cats = $cats->time('updated_at');
			else $cats = $cats->time('created_at');
		}
		
		$cats = $cats->offset($offset)->limit($limit)->get();
	

		if($field)
	 		$cats->each(function($cat) use ($fields){
	 			$cat->setVisible($fields);	
	 		});
	 	
	 	return Response::listing(
	 		array(
	 			'code'=>200,
	 			'message'=> 'success'
	 		),
	 		$cats, $offset, $limit
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
		$validator = Validator::make(Input::all(), Category::$rules['save'], Category::$messages);

		if ($validator->passes()) {
			$cat = Category::create(array(
				'name' => Input::get('name'),
				'app_id' => Appl::getAppIDByKey(Input::get('appkey')),
				'parent_id' => Input::get('parent_id')
			));

			$picture = Input::get('picture', null);
			if($picture)
			{
				$response = Image::upload($picture);
				if(is_object($response)) return $response;
				$app->picture = $response;
			}

			if($cat)
				return Response::result(array(
					'header'=> array(
		        		'code'=> 200,
		        		'message'=> 'success'
		        	), 'id'=> $cat->id
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
		$cat = Category::find($id);

		if($cat)
		{
			$cat['children'] = $cat->getDescendants()->toHierarchy()->toArray();
			return Response::result(
				array(
	        		'header' => array(
	        			'code' => 200,
	        			'message' => 'success'
	        		),
	        		// #Fixed Collection with key in Baum\Extensions\Eloquent\Collection.
	        		'entry' => $cat->toArray()
	        	)
			);
		}
		return Response::message(204, 'Category id: '. $id .' does not exists.'); 
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
				return Response::message(200, 'Updated category_id: '.$id.' success!'); 
		}

		return Response::message(400, $validator->messages()->first()); 

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
		$validator = Validator::make(array( 'id' => $id), Category::$rules['delete']);

		if ($validator->passes()) {
			Category::find($id)->delete();
			return Response::message(200, 'Deleted category_'.$id.' success!'); 
		}

		return Response::message(400, $validator->messages()->first()); 
	}
}