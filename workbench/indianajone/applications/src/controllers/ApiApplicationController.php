<?php namespace Indianajone\Applications\Controllers;

use Appl, Image, Input, Response;
use Indianajone\Applications\AppRepositoryInterface;

class ApiApplicationController extends \BaseController
{
	public function __construct(AppRepositoryInterface $apps)
	{
		parent::__construct();
		$this->apps = $apps;
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		if($this->apps->validate('show'))
		{
			$apps = $this->apps->all();
			return Response::result(
                array(
                    'header'=> array(
                        'code'=> 200,
                        'message'=> 'success'
                    ),
                    'offset' => (int) Input::get('offset', 0),
                    'limit' => (int) Input::get('limit', 10), 
                    'total' => (int) $this->apps->countWithChildren(),
                    'entries' => $apps
                )
            ); 
		}

		return Response::message(400, $this->apps->errors());
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
		if($this->apps->validate('create'))
		{
			$id = $this->apps->create(array(
				'name' => Input::get('name'),
				'user_id' => Input::get('user_id'),
				'description' => Input::get('description', ''),
				'appkey' => Appl::genKey(),
				'picture' => Input::get('picture')
			));

			if($id) 
			{
				return Response::result(
					array(
						'header'=> array(
			        		'code'=> 200,
			        		'message'=> 'success'
			        	),
						'id'=> $id
					)
				);
			}

			return Response::message(500, $this->apps->errors());
		}

		return Response::message(400, $this->apps->errors());		
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		$input = array_add(Input::all(), 'id',$id);

		if($this->apps->validate('show', $input))
		{	
			return Response::result(
				array(
                    'header'=> array(
                        'code'=> 200,
                        'message'=> 'success'
                    ),
                    'entry' => $this->apps->find($id)
                )
			);

			return Response::message(403, 'Unauthorize user');
		}

		return Response::message(400, $this->apps->errors());
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
		if($this->apps->validate('update', $input))
		{
			$app = $this->apps->update($id, Input::all());
			
			if($app)
				return Response::message(200, 'Updated app id: '.$id.' success!');

			return Response::message(404, 'Selected application does not exists.');
		}

		return Response::message(400, $this->apps->errors());
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

		if ($this->apps->validate('delete', $input)) 
		{
			$app = $this->apps->delete($id);
			return Response::message(200, 'Deleted Application: '.$id.' success!');
		}

		return Response::message(400, $this->apps->errors()); 
	}

}