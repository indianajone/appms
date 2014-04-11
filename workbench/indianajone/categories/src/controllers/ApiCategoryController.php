<?php namespace Indianajone\Categories\Controllers;

use Appl, Input, Response;
use Indianajone\Categories\Repositories\CategoryRepositoryInterface;
use \Indianajone\Categories\Category;

class ApiCategoryController extends \BaseController
{
	public function __construct(CategoryRepositoryInterface $cats)
	{
		parent::__construct();
		$this->cats = $cats;
	}
	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		if($this->cats->validate('show'))
		{
			$cats = $this->cats->all();

			return Response::result(array(
				'header' => array(
					'code' => 200,
					'message' => 'success'
				),
				'offset' => (int) Input::get('offset', 0),
                'limit' => (int) Input::get('limit', 10), 
                'total' => (int) $this->cats->count(),
                'entries' => $cats->toArray()
			));
		}

		return Response::message(400, $this->cats->errors());
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
		if($this->cats->validate('create'))
		{
			$cat = $this->cats->create(array(
				'name' => Input::get('name'),
				'description' => Input::get('description', null),
				'app_id' => Appl::getAppIDByKey(Input::get('appkey')),
			));

			if(is_null($cat)) 
				return Response::message(500, 'Something wrong when trying to create category.');

			if( $cat->save() )
			{
				$parent = Input::get('parent_id', null);
				if( !is_null($parent) )
					$cat->updateParent($parent);

				return Response::result(
					array(
						'header'=> array(
			        		'code'=> 200,
			        		'message'=> 'success'
			        	),
						'id'=> $cat->id
					)
				);
			}
		}

		return Response::message(400, $this->cats->errors());
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		$input = array_add(Input::all(), 'id', $id);

		if($this->cats->validate('delete', $input))
		{
			$cat = $this->cats->find($id);
			return Response::result(
				array(
	        		'header' => array(
	        			'code' => is_null($cat) ? 204 : 200,
	        			'message' => is_null($cat) ? 'no content' : 'success',
	        		),
	        		'entry' => $cat
	        	)
			);
		}

		return Response::message(400, $this->cats->errors());
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

		if($this->cats->validate('update', $input))
		{
			$cat = $this->cats->update($id, $input);
			
			if(!is_null($cat))
			{	
				if($cat->save())
				{
					$parent = Input::get('parent_id', null);
					if( !is_null($parent) )
						$cat->updateParent($parent);
					
					return Response::message(200, 'Updated cat id: '.$id.' success!');
				}
			}

            return Response::message(404, 'Selected application does not exists.');
		}

		return Response::message(400, $this->cats->errors());
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
		$input = array_add(Input::all(), 'id', $id);

		if($this->cats->validate('delete', $input))
		{
			if( $this->cats->delete($id) )
				return Response::message(200, 'Deleted Category: '.$id.' success!');
		}

		return Response::message(400, $this->cats->errors());
	}
}