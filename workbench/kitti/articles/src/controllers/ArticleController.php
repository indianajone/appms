<?php namespace Kitti\Articles\Controllers;

use Appl, Input, Response, Validator;
use Carbon\Carbon;
use Kitti\Articles\Repositories\ArticleRepositoryInterface;

class ArticleController extends \BaseController
{
	public function __construct(ArticleRepositoryInterface $articles)
	{
		$this->articles = $articles;
		parent::__construct();
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
					'total' => $this->articles->count(),
					'entries' => $this->articles->all()->toArray()
				)
			);
		}

		return Response::message(400, $this->articles->errors()); 
	}

	public function create()
	{
		return $this->store();
	}

	public function store()
	{
		if($this->articles->validate('create'))
		{
			$article = $this->articles->create(array(
				'app_id' => Appl::getAppIDByKey(Input::get('appkey')),
				'gallery_id' => Input::get('gallery_id', null),
				'pre_title' => Input::get('pre_title'),
				'title' => Input::get('title'),
				'teaser' => Input::get('teaser'),
				'content' => Input::get('content'),
				'wrote_by' => Input::get('wrote_by'),
				'published_at' => Input::get('published_at', Carbon::now()->timestamp),
				'tags' => Input::get('tags')
			));

			if( $article->save() )
			{
				if(Input::get('picture', null))
	            {
	            	$response = $article->createPicture($article->app_id);
	            	if(is_object($response)) return $response;             	
	            }

				$category_id = Input::get('category_id', null);
				if($category_id)
				{
					$ids = explode(',', $category_id); 
					$article->attachCategories($ids);
				}

				return Response::result(array(
					'header'=> array(
		        		'code'=> 200,
		        		'message'=> 'success'
		        	), 'id'=> $article->id
				));
			}

			return Response::message(500, 'Something wrong while creating an article.');
		}

		return Response::message(400, $this->articles->errors());
	}

	public function show($id)
	{
		$input = array_add(Input::all(), 'id', $id);

		if( $this->articles->validate('show_with_id', $input) )
		{
			$article = $this->articles->find($id);

			return Response::result(array(
        		'header' => array(
        			'code' => 200,
        			'message' => 'success'
        		),
        		'entry' => $article->toArray()
        	));
		}

		return Response::message(400, $this->articles->errors());
	}

	public function edit($id)
	{
		return $this->update($id);
	}

	public function update($id)
	{
		$input = array_add(Input::all(), 'id', $id);

		if( $this->articles->validate('update', $input) )		
		{
			$article = $this->articles->update($id, $input);
			
			if( $article instanceof \Illuminate\Http\JsonResponse )
				return $article;

			$category_id = Input::get('category_id', null);
			if($category_id)
			{
				$ids = explode(',', $category_id); 
				$article->syncRelations('categories', $ids);
			}

			if($article->save())
				return Response::message(200, 'Updated article_id: '.$id.' success!');

			return Response::message(500, 'Something wrong while trying to update.');
		}

		return Response::message(400, $this->articles->errors());
	}

	public function delete($id)
	{
		return $this->destroy($id);
	}

	public function destroy($id)
	{
		$inputs = array_add(Input::all(), 'id', $id);
		$validator = Validator::make($inputs, Article::$rules['delete']);

		if($validator->passes())
		{
	        $article = Article::where('id','=',$id)->app()->first();
	        if($article)
	        {
	        	if($article->gallery) $artcle->gallery()->delete();
	        	$article->detachCategories(array_flatten($article->getCategoryIds()));	
				$article->delete();

				return Response::message(200, 'Deleted article_id: '.$id.' success!'); 
	        }
			
			return Response::message(204, 'article_id: '.$id.' does not exists!'); 
		}

		return Response::message(400, $validator->messages()->first());
	}
}