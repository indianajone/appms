<?php namespace Kitti\Articles\Controllers;

use Input, Response;
use Kitti\Articles\Repositories\ArticleRepositoryInterface;
use Indianajone\Share\Repositories\ShareRepositoryInterface;

class ShareArticleController extends \BaseController
{
	public function __construct(
		ArticleRepositoryInterface $articles,
		ShareRepositoryInterface $shares)
	{
		$this->articles = $articles;
		$this->shares = $shares;
	}

	public function index($id)
	{
		$input = array_add(Input::all(), 'content_id', $id);
		$input = array_add($input, 'content_type', 'article');

		return $this->provider('facebook', $input);
	}

	public function provider($provider, $input=null)
	{
		$cls = 'Indianajone\\Share\\Providers\\'.ucfirst($provider).'Provider';

		if( class_exists($cls) )
		{
			if( $this->shares->validate('share', $input) )
			{
				$this->provider = new $cls;
				$result = $this->provider->share($input);

				// dd($result);
				return Response::result(array_merge(array(
					'header' => array(
						'code' 		=> 200,
						'message' 	=> 'success'
					))
				, compact('result')));
			}

			return Response::message(400, $this->shares->errors());
		}

		return Response::message(400, 'Sharing to '. $provider . ' has not support yet');
		
	}
}