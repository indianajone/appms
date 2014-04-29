<?php namespace Kitti\Articles\Controllers;

use Input, Response;
use Kitti\Articles\Repositories\ArticleRepositoryInterface;

class ApiArticleController extends \BaseController
{
	public function __construct(ArticleRepositoryInterface $articles)
	{
		parent::__construct();
		$this->articles = $articles;
	}

	public function index()
	{
		if($this->articles->validate('show'))
		{
			return Response::result(
				array(
					'header'=> array(
		        		'code'=> 200,
		        		'message'=> 'success'
		        	),
					'offset' => (int) Input::get('offset', 0),
					'limit' => (int) Input::get('limit', 10),
					'total' => (int) $this->articles->count(),
					'entries' => $this->articles->all()->toArray()
				)
			);
		}

		return Response::message(400, $this->articles->errors()); 
	}
}