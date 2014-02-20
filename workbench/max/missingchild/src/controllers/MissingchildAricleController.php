<?php namespace Max\Missingchild\Controllers;

use Validator, Input, Response, Hash, Appl, Image;
use Indianajone\Categories\Category;
use Max\Missingchild\Models\Missingchild as Child;
use Baum\Extensions\Eloquent\Collection;
use Kitti\Articles\Article;
use Kitti\Galleries\Gallery;
use Carbon\Carbon;


class MissingchildArticleController extends \BaseController 
{
	public function index($id)
	{
		$inputs = array_add(Input::all(), 'id', $id);
		$validator = Validator::make($inputs, Child::$rules['show_with_id']);

		if($validator->passes())
		{
			$child = Child::app()->whereId($id)->first();
			$articles = $child->articles()->with('categories', 'gallery.medias')->get();

			foreach ($articles as $item => $article) {
            	$article->fields();
            	$article->setHidden(array_merge($article->getHidden() ,array('categories')));
            	$types = $article->categories()->get(); //->remember(1)
            	$obj = [];
            	foreach ($types as $type) {
            		if(!$type->isRoot())
            		{
            			$name = Category::whereId($type->getParentId())->first()->name; //->remember(1)
            			if(!array_key_exists($name,$obj)) 
            				$obj[$name] = [];

            			array_push($obj[$name], $type->toArray());
            		}

            		foreach ($obj as $key => $type) {
            			$article->setRelation($key, new Collection($type));
            		}
            	}
            }

			return Response::result(array(
        		'header' => array(
        			'code' => 200,
        			'message' => 'success'
        		),
        		'entry' => !$articles->isEmpty() ? $articles->toArray() : null
        	));

		}

		return Response::message(400, $validator->messages()->first());
	}

	public function create()
	{
		return $this->store();
	}

	public function store()
	{
		$inputs = array_add(Input::all(), 'id', $id);
		$rules = array_merge(Child::$rules['show_with_id'], Article::$rules['create']);
		$validator = Validator::make($inputs, $rules);
		
		if($validator->passes())
		{
			$child = Child::active()->whereId($id)->first();
			$child->articles()->create(array(

			));
		}

		return Response::message(400, $validator->messages()->first());
	}

	public function attachArticles($id)
	{
		$inputs = array_add(Input::all(), 'id', $id);
		$validator = Validator::make($inputs, Child::$rules['create_clue']);
		if($validator->passes())
		{
			$child = Child::active()->whereId($id)->first();
			$ids = explode(',', Input::get('article_id')); 
			$child->attachRelations('articles',$ids);

			return Response::message(200, 'Added Clue to missingchild_id: ' . $id . ' Success!');
		}

		return Response::message(400, $validator->messages()->first());
	}

	public function detachArticles($id)
	{
		$inputs = array_add(Input::all(), 'id', $id);
		$validator = Validator::make($inputs, Child::$rules['create_clue']);
		if($validator->passes())
		{
			$child = Child::active()->whereId($id)->first();
			$ids = explode(',', Input::get('article_id')); 
			$child->detachRelations('articles',$ids);

			return Response::message(200, 'Added Clue to missingchild_id: ' . $id . ' Success!');
		}

		return Response::message(400, $validator->messages()->first());
	}


	public function edit($id, $article_id)
	{
		return $this->update($id, $article_id);
	}

	public function update($id, $article_id)
	{
		$inputs = array_merge(Input::all(), array('id' => $id, 'article_id' => $article_id));
		$validator = Validator::make($inputs, Child::$rules['create_clue']);
	}

	public function delete($id)
	{
		return $this->destroy($id);
	}

	public function destroy($id)
	{
		return 'destroy';
	}

}