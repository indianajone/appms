<?php namespace Max\Missingchild\Controllers;

use Validator, Input, Response, Hash, Appl, Image;
use Indianajone\Categories\Category;
use Max\Missingchild\Models\Missingchild as Child;
use Baum\Extensions\Eloquent\Collection;
use Kitti\Articles\Article;
use Kitti\Galleries\Gallery;
use Carbon\Carbon;


class MissingchildController extends \BaseController {

	public function index()
	{
		$validator = Validator::make(Input::all(), Child::$rules['show']);

        if($validator->passes())
        {
            $children = Child::active()->apiFilter()->with('gallery.medias')->get(); 

            foreach ($children as $item => $child) {
            	$child->fields();
            	$types = $child->categories()->remember(1)->get();
            	$obj = [];
            	foreach ($types as $type) {
            		if(!$type->isRoot())
            		{
            			$name = Category::whereId($type->getParentId())->remember(1)->first()->name;
            			if(!array_key_exists($name,$obj)) 
            				$obj[$name] = [];

            			array_push($obj[$name], $type->toArray());
            		}

            		foreach ($obj as $key => $type) {
            			$child->setRelation($key, new Collection($type));
            		}
            	}

            	$content = $child->app_content()->get();

            	if($content->count() >= 1) 
            		$child->setRelation('app_content', $content->load('gallery.medias')->first());
            	
            }

            // dd(\DB::getQueryLog());

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

	public function store()
	{
		$validator = Validator::make(Input::all(), Child::$rules['create']);

 		if($validator->passes())
 		{
 			$app_id = Appl::getAppIDByKey(Input::get('appkey'));
 			$child = Child::create(array(
 				'app_id' => $app_id,
 				// 'title' => Input::get('title'),
 				'article_id' => null,
 				'description' => Input::get('content'),
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
 				'missing_at' => Input::get('missing_date'),
 				'reported_place' => 'สถานีห้วยขวาง',
 				'reported_at' => Input::get('report_date')
 			));

			$category_id = Input::get('category_id', null);
			if($category_id) 
			{
				$ids = explode(',', $category_id); 
				$child->attachRelations('categories',$ids);
			}

			$child->gallery()->create(
                array(
                    'app_id' => $app_id,
                    'content_id' => $child->id,
                    'content_type' => 'child',
                    'name' => $child->first_name .'\'s gallery',             
                    'publish_at' => Input::get('publish_at', Carbon::now()->timestamp),
                )
            );

            $picture = Input::get('picture', null);
			if($picture)
			{
				if(filter_var($picture, FILTER_VALIDATE_URL))
				{
					$child->picture = $picture;
				}
				else if(base64_decode($picture, true))
				{
					dd('yeah');
					$response = Image::upload($picture);
					if(is_object($response)) return $response;
					$child->picture = $response;
				}
				else
				{
					dd('id');
				}
			}

			$child->app_content()->create(array(
				'app_id' => $app_id,
				'title' => Input::get('title'),
				'content' => $child->description,
				'wrote_by' => Input::get('wrote_by', 'Admin'),
				'publish_at' => Input::get('publish_at', Carbon::now()->timestamp),
			));

			// $article_type = Input::get('article_type', null);
			// if($article_type) $article->attachCategory($article_type);

			// $child->attachRelation('articles',$article->id);

			// $article_gallery = Gallery::create(
   //              array(
   //                  'app_id' => $app_id,
   //                  'content_id' => $article->id,
   //                  'content_type' => 'article',
   //                  'name' => $child->first_name .'\'s article images',             
   //                  'publish_at' => Input::get('publish_at', Carbon::now()->timestamp),
   //              )
   //          );

            // $article->update(array(
            // 	'article_id' = $child->app
            // 	// 'gallery_id' => $article_gallery->id
            // ));
            
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
			$child = Child::apiFilter()->with('gallery.medias', 'articles.gallery.medias')->find($id);
			$child->fields();
        	
        	$types = $child->getRelation('categories');
        	$obj = array();
        	foreach ($types as $type) {
        		if(!$type->isRoot())
        		{
        			$name = $type->getRoot()->name;
        			if(!array_key_exists($name,$obj)) 
        				$obj[$name] = [];

        			array_push($obj[$name], $type->toArray());
        		}
        	}
        	
        	foreach ($obj as $key => $type) {
        		$child->setRelation($key, new Collection($type));
        	}

        	$content = $child->app_content()->get();

        	if($content->count() >= 1) 
        		$child->setRelation('app_content', $content->load('gallery.medias')->first());

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

            $picture = Input::get('picture', null);
			if($picture)
			{
				if(filter_var($picture, FILTER_VALIDATE_URL))
				{
					$child->picture = $picture;
				}
				else
				{
					$response = Image::upload($picture);
					if(is_object($response)) return $response;
					$child->picture = $response;
				}
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

			/**
				#TODO Delete related content too.
			**/
	        $child = Child::find($id);
        	// Detach Types
        	$ids = $child->types()->get()->lists('id');
        	$child->detachRelations('categories', $ids);

        	$ids = $child->articles->lists('id');
        	var_dump($ids);
        	// $articles = $child->articles;
        	$child->detachRelations('articles', $ids);
        	

        	// foreach ($articles as $article) {
        		// $artic
        	// }

        	var_dump($child->gallery->id);
        	// exit;
			// Delete child.
			if($child->delete())
			{
				// var_dump($child->gallery->id);

				return Response::message(200, 'Deleted missingchild_'.$id.' success!'); 
			}

			return Response::message(204, 'missingchild_id:'.$id.' does not exists!'); 
		}

		return Response::message(400, $validator->messages()->first());
	}

}