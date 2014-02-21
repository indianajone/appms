<?php namespace Max\Missingchild\Controllers;

use Validator, Input, Response, Hash, Appl, Image;
use Indianajone\Categories\Category;
use Max\Missingchild\Models\Missingchild as Child;
use Baum\Extensions\Eloquent\Collection;
// use Max\Missingchild\Collection;
use Kitti\Articles\Article;
use Kitti\Galleries\Gallery;
use Carbon\Carbon;


class MissingchildController extends \BaseController 
{
	public function index()
	{
		$validator = Validator::make(Input::all(), Child::$rules['show']);

        if($validator->passes())
        {
        	$children = Child::app()->apiFilter()->get()->load('categories', 'gallery.medias', 'app_content.gallery.medias');
        	// $children = Child::app()->apiFilter()->with('categories')->get();

        	$children->each(function($child)
        	{
        		if($child->categories)
        		{
        			foreach($child->categories as $category)
        			{
        				if(!$category->isRoot())
        				{
        					$root = $category->getRoot();
        					$relation = $category->getDescendantsAndSelf();
        					if($relation->count() > 0)
        						$child->setRelation($root->name, $relation->toHierarchy());
        					else
        						$child->setRelation($root->name, null);
        				}
        				else
        				{
        					$child->setRelation($category->name, $category->getDescendants()->toHierarchy());
        				}
        			}
        		}

        		if($child->gallery)
        		{
	        		$child->gallery->setVisible(array(
	        			'id',
	        			'name',
	        			'description',
	        			'picture',
	        			'medias'
	        		));

	        		if($child->gallery->medias)
	        		{
		        		$child->gallery->medias->each(function($media) 
		        		{
		        			$media->setVisible(array(
			        			'id',
			        			'name',
			        			'link',
			        			'picture'
			        		));
		        		});
		        	}
	        	}

	        	if($child->app_content)
	        	{
	        		if($child->app_content->gallery)
	        		{
	        			$child->app_content->gallery->setVisible(array(
		        			'id',
		        			'name',
		        			'description',
		        			'picture',
		        			'medias'
		        		));

			        	$child->app_content->gallery->medias->each(function($media) 
		        		{
		        			$media->setVisible(array(
			        			'id',
			        			'name',
			        			'link',
			        			'picture'
			        		));
		        		});
	        		}
        			
	        	}
        	});

            // var_dump(\DB::getQueryLog());

            return Response::result(
				array(
					'header'=> array(
		        		'code'=> 200,
		        		'message'=> 'success'
		        	),
					'offset' => (int) Input::get('offset', 0),
					'limit' => (int) Input::get('limit', 10),
					'total' => Child::count(),
					'entries' => $children->toArray()
				)
			); 
        }

         return Response::message(204, $validator->messages()->first());
	}

	public function create()
	{
		return $this->store();
	}

	// public function articles($id)
	// {
	// 	$inputs = array_add(Input::all(), 'id', $id);
	// 	$validator = Validator::make($inputs, Child::$rules['show_with_id']);

	// 	if($validator->passes())
	// 	{
	// 		$child = Child::app()->whereId($id)->first();
	// 		$articles = $child->articles()->with('categories', 'gallery.medias')->get();

	// 		dd(\DB::getQueryLog());

	// 		foreach ($articles as $item => $article) {
 //            	$article->fields();
 //            	$article->setHidden(array_merge($article->getHidden() ,array('categories')));
 //            	$types = $article->categories()->get(); //->remember(1)
 //            	$obj = [];
 //            	foreach ($types as $type) {
 //            		if(!$type->isRoot())
 //            		{
 //            			$name = Category::whereId($type->getParentId())->first()->name; //->remember(1)
 //            			if(!array_key_exists($name,$obj)) 
 //            				$obj[$name] = [];

 //            			array_push($obj[$name], $type->toArray());
 //            		}

 //            		foreach ($obj as $key => $type) {
 //            			$article->setRelation($key, new Collection($type));
 //            		}
 //            	}
 //            }

	// 		return Response::result(array(
 //        		'header' => array(
 //        			'code' => 200,
 //        			'message' => 'success'
 //        		),
 //        		'entry' => !$articles->isEmpty() ? $articles->toArray() : null
 //        	));

	// 	}

	// 	return Response::message(400, $validator->messages()->first());
	// }

	// public function createArticles($id)
	// {
	// 	$inputs = array_add(Input::all(), 'id', $id);
	// 	$rules = array_merge(Child::$rules['show_with_id'], Article::$rules['create']);
	// 	$validator = Validator::make($inputs, $rules);
		
	// 	if($validator->passes())
	// 	{
	// 		$child = Child::active()->whereId($id)->first();
	// 		$child->articles()->create(array(

	// 		));
	// 	}

	// 	return Response::message(400, $validator->messages()->first());
	// }

	// public function attachArticles($id)
	// {
	// 	$inputs = array_add(Input::all(), 'id', $id);
	// 	$validator = Validator::make($inputs, Child::$rules['create_clue']);
	// 	if($validator->passes())
	// 	{
	// 		$child = Child::active()->whereId($id)->first();
	// 		$ids = explode(',', Input::get('article_id')); 
	// 		$child->attachRelations('articles',$ids);

	// 		return Response::message(200, 'Added Clue to missingchild_id: ' . $id . ' Success!');
	// 	}

	// 	return Response::message(400, $validator->messages()->first());
	// }

	// public function detachArticles($id)
	// {
	// 	$inputs = array_add(Input::all(), 'id', $id);
	// 	$validator = Validator::make($inputs, Child::$rules['create_clue']);
	// 	if($validator->passes())
	// 	{
	// 		$child = Child::active()->whereId($id)->first();
	// 		$ids = explode(',', Input::get('article_id')); 
	// 		$child->detachRelations('articles',$ids);

	// 		return Response::message(200, 'Added Clue to missingchild_id: ' . $id . ' Success!');
	// 	}

	// 	return Response::message(400, $validator->messages()->first());
	// }

	public function store()
	{
		$validator = Validator::make(Input::all(), Child::$rules['create']);

 		if($validator->passes())
 		{
 			$app_id = Appl::getAppIDByKey(Input::get('appkey'));

 			$child = Child::create(array(
 				'app_id' => $app_id,
 				'description' => Input::get('description'),
 				'first_name' => Input::get('first_name'),
 				'last_name' => Input::get('last_name'),
 				'nickname' => Input::get('nickname'),
 				'gender' => Input::get('gender'),
 				'lost_age' => Input::get('lost_age'),
 				'age' => Input::get('age'),
 				'place_of_missing' => Input::get('place_of_missing'),
 				'latitude' => Input::get('latitude'),
 				'longitude' => Input::get('longitude'),
 				'note' => Input::get('note'),
 				'order' => Input::get('order', 0),
 				'missing_at' => Input::get('missing_at'),
 				'reported_place' => 'สถานีห้วยขวาง',
 				'reported_at' => Input::get('reported_at')
 			));

			$child->attachRelations('categories',Input::get('category_id'));

			$child->gallery()->create(
                array(
                    'app_id' => $app_id,
                    'content_id' => $child->id,
                    'name' => $child->first_name .'\'s gallery',             
                    'publish_at' => Input::get('publish_at', Carbon::now()->timestamp),
                )
            );

           if(Input::get('picture', null))
            {
            	$response = $child->createPicture($app_id);
            	if(is_object($response)) return $response;             	
             	unset($inputs['picture']);
            }

			$app_content = Article::create(array(
				'app_id' => $app_id,
				'title' => Input::get('title'),
				'content' => $child->description,
				'wrote_by' => Input::get('wrote_by', 'Admin'),
				'publish_at' => Input::get('publish_at', Carbon::now()->timestamp),
			));

			$child->update(array(
				'article_id' => $app_content->id
			));

			$app_content->gallery()->create(array(
				'app_id' => $app_id,
				'content_id' => $app_content->id,
				'name' => $app_content->id,
				'publish_at' => Input::get('publish_at', Carbon::now()->timestamp)
			));
            
			if($child->save())
				return Response::result(
					array(
						'header'=> array(
			        		'code'=> 200,
			        		'message'=> 'success'
			        	),
						'id'=> $child->id
					)
				); 

 		}

 		return Response::message(400, $validator->messages()->first());
	}

	public function show($id)
	{
		$inputs = array_add(Input::all(),'id',$id);
		$validator = Validator::make($inputs, Child::$rules['show_with_id']);

		if($validator->passes())
		{
			$child = Child::apiFilter()->with('categories', 'gallery.medias', 'app_content.gallery.medias', 'articles')->find($id);
			$child->fields();

			$types = $child->getRelation('categories');
        	$obj = array();
        	foreach ($types as $type) {
        		$type->setVisible(array('id','name'));
        		if(!$type->isRoot())
        		{
        			$name = $type->getRoot()->name;
        			if(!array_key_exists($name,$obj)) 
        				$obj[$name] = [];

        			array_push($obj[$name], $type->toArray());
        		}
        		else
        		{
        			$name = $type->name;
        			$obj[$name] = $type->toArray();
        		}
        	}
        	
        	foreach ($obj as $key => $type) {
        		$child->setRelation($key, new Collection($type));
        	}  	

			return Response::result(array(
	        		'header' => array(
	        			'code' => 200,
	        			'message' => 'success'
	        		),
	        		'entry' => $child->toArray()
	        	));
		}

		return Response::message(400, $validator->messages()->first()); 
	}

	public function edit($id)
	{
		return $this->update($id);
	}

	public function update($id)
	{
		$inputs = array_add(Input::all(), 'id', $id);
        $validator = Validator::make($inputs, Child::$rules['update']);

        if($validator->passes())
        {
        	$child = Child::find($id);

            foreach ($inputs as $key => $val) {
                if( $val == null || 
                    $val == '' || 
                    $val == $child[$key] ||
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
				$child->syncRelations('categories', $ids);
				unset($inputs['category_id']);
			}


            if(array_key_exists('picture', $inputs))
            {
            	$response = $child->createPicture($child->app_id);
            	if(is_object($response)) return $response;             	
             	unset($inputs['picture']);
            }

            if($child->update($inputs))
                 return Response::message(200, 'Updated missingchild id: '.$id.' success!');
        }

        return Response::message(400,$validator->messages()->first());
	}

	public function delete($id)
	{
		return $this->destroy($id);
	}

	public function destroy($id)
	{
		$inputs = array_add(Input::all(), 'id', $id);
		$validator = Validator::make($inputs, Child::$rules['delete']);

		if($validator->passes())
		{
	        $child = Child::find($id);
        	
			if($child->delete())
			{
				return Response::message(200, 'Deleted missingchild_'.$id.' success!'); 
			}

			return Response::message(204, 'missingchild_id:'.$id.' does not exists!'); 
		}

		return Response::message(400, $validator->messages()->first());
	}

}