<?php namespace Kitti\Articles\Controllers;

use \Appl;
use \BaseController;
use \Carbon\Carbon;
use \Input;
use \Response;
use \Validator;
use \Kitti\Articles\Article;
use \Indianajone\Categories\Category as Categories;

class ArticleController extends BaseController
{
	public function index()
	{
		$validator = Validator::make(Input::all(), Article::$rules['show']);

		$offset = Input::get('offset', 0);
		$limit= Input::get('limit', 10);
		$field = Input::get('fields', null);
		$fields = explode(',', $field);

		if($validator->passes())
		{
			$categories = Input::get('category_id', null);
			if(!$categories)
				$articles = Article::active()->app()->offset($offset)->limit($limit)->with('categories')->get();

			else 
				$articles = Article::whereCat(explode(',',$categories))->get();

			// $article = Article::with('categories')->whereCat(array(1))->get();

			// dd(\DB::getQueryLog());

			$articles->each(function($article) use ($fields, $field){
	 			if($field) $article->setVisible($fields);
	 			// $article->categories->toHierarchy();
	 		});

	 		return Response::listing(
		 		array(
		 			'code' 		=> 200,
		 			'message' 	=> 'success'
		 		),
		 		$articles, $offset, $limit
		 	);
		}

		return Response::message(400, $validator->messages()->first()); 
	}

	public function create()
	{
		return $this->store();
	}

	public function store()
	{
		Validator::resolver(function($translator, $data, $rules, $messages)
		{
		    return new \Indianajone\Validators\Rules\ExistLoop($translator, $data, $rules, $messages);
		});

		$inputs = Input::all();
		$validator = Validator::make($inputs, Article::$rules['create']);

		if($validator->passes())
		{
			$article = Article::create(array(
				'app_id' => Appl::getAppIDByKey(Input::get('appkey')),
				'gallery_id' => Input::get('gallery_id', null),
				'pre_title' => Input::get('pre_title'),
				'title' => Input::get('title'),
				'teaser' => Input::get('teaser'),
				'content' => Input::get('content'),
				'wrote_by' => Input::get('wrote_by'),
				'publish_at' => Input::get('publish_at', Carbon::now()->timestamp),
				'tags' => Input::get('tags')
			));

			$picture = Input::get('picture', null);
			if($picture)
			{
				$response = Image::upload($picture);
				if(is_object($response)) return $response;
				$article->picture = $response;
			}

			$category_id = Input::get('category_id', null);
			if($category_id)
			{
				$ids = explode(',', $category_id); 
				$article->attachCategories($ids);
			}

			if($article)
				return Response::result(array(
					'header'=> array(
		        		'code'=> 200,
		        		'message'=> 'success'
		        	), 'id'=> $article->id
				));
		}

		return Response::message(400, $validator->messages()->first()); 
	}

	public function show($id)
	{
		# code...
	}

	public function edit($id)
	{
		$this->update($id);
	}

	public function update($id)
	{
		# code...
	}

	public function delete($id)
	{
		$this->destroy($id);
	}

	public function destroy($id)
	{
		# code...
	}

}