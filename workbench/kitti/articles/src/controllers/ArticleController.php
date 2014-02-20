<?php namespace Kitti\Articles\Controllers;

use App;
use Appl;
use BaseController;
use Carbon\Carbon;
use Input;
use Response;
use Validator;
use Kitti\Articles\Article;
use Indianajone\Categories\Category;
use Indianajone\Categories\Extensions\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;

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
			$articles = Article::app()->active()->ApiFilter()->offset($offset)->limit($limit)->get();
			
			$articles->each(function($article) {
				$article->fields();

				$cats = $article->categories()->get(); 
				foreach ($cats as $key => $cat) {
					$article->setRelation($cat->getRoot()->name, $cat->getAncestorsAndSelfWithoutRoot()->toHierarchy());
				}

				$gallery = $article->gallery()->with('medias')->first();
				if($gallery) $article->setRelation('gallery', $gallery);
			
	 		});

	 		// dd(\DB::getQueryLog());

		 	return Response::result(
				array(
					'header'=> array(
		        		'code'=> 200,
		        		'message'=> 'success'
		        	),
					'offset' => (int) Input::get('offset', 0),
					'limit' => (int) Input::get('limit', 10),
					'total' => Article::count(),
					'entries' => $articles->toArray()
				)
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
		$inputs = array_add(Input::all(),'id',$id);
		$validator = Validator::make($inputs, Article::$rules['show_with_id']);

		if($validator->passes())
		{
			$article = Article::whereId($id)->app()->active()->ApiFilter()->first();
			if($article)
			{
				$article->fields();
				$cats = $article->categories()->get(); 
				foreach ($cats as $key => $cat) {
					$article->setRelation($cat->getRoot()->name, $cat->getAncestorsAndSelfWithoutRoot()->toHierarchy());
				}

				$gallery = $article->gallery()->with('medias')->first();
				if($gallery) $article->setRelation('gallery', $gallery);

				return Response::result(array(
	        		'header' => array(
	        			'code' => 200,
	        			'message' => 'success'
	        		),
	        		'entry' => $article->toArray()
	        	));
	        }

	        return Response::message(204, 'Article id:'. $id . ' does not exists.');
		}

		return Response::message(400, $validator->messages()->first()); 
	}

	public function edit($id)
	{
		return $this->update($id);
	}

	public function update($id)
	{
		$inputs = array_merge(array('id'=>$id), Input::all());
		$validator = Validator::make($inputs, Article::$rules['update']);	

		if($validator->passes())
		{
			$article = Article::where('id','=',$id)->app()->first();

			$inputs = Input::all();
			foreach ($inputs as $key => $val) {
                if( $val == null || 
                    $val == '' || 
                    $val == $article[$key] ||
                    $key == 'appkey' ||
                    $key == 'id') 
                {
                    unset($inputs[$key]);
                }
            }

            if(!count($inputs))
                return Response::message(200, 'Nothing is update.');

            $category_id = Input::get('category_id', null);
			if($category_id)
			{
				$ids = explode(',', $category_id); 
				$article->attachCategories($ids);
				unset($inputs['category_id']);
			}

			$article->update($inputs);

            if($article->save())
            	return Response::message(200, 'Updated article_id: '.$id.' success!');

            return Response::message(500, 'Something wrong while trying to update.');
		}

		return Response::message(400, $validator->messages()->first());
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
	        	$article->gallery()->first()->delete();
	        	$article->detachCategories(array_flatten($article->getCategoryIds()));	
				$article->delete();

				return Response::message(200, 'Deleted article_'.$id.' success!'); 
	        }
			
			return Response::message(204, 'article_'.$id.' does not exists!'); 
		}

		return Response::message(400, $validator->messages()->first());
	}
}