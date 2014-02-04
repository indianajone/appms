<?php namespace Max\Missingchild\Controllers;

use Validator, Input, Response, Hash, Appl;
use Indianajone\Categories\Category;
use Max\Missingchild\Models\Missingchild as Child;
use \Kitti\Galleries\Gallery;
use Carbon\Carbon;


class MissingchildController extends \BaseController {

	public function index()
	{
		$validator = Validator::make(Input::all(), Child::$rules['show']);

        if($validator->passes())
        {

        	$offset = Input::get('offset', 0);
            $limit = Input::get('limit', 10);
            $field = Input::get('fields', null);
            $fields = $field ? explode(',', $field) : $field;
            
            $updated_at = Input::get('updated_at', null);
            $created_at = Input::get('created_at', null);

            $children = Child::active();

            $keyword = Input::get('q', null);
			if($keyword)
				$children = $children->where('first_name', 'like', '%'.$keyword.'%')->orWhere('last_name','like', '%'.$keyword.'%');

            $categories = Input::get('category_id', null);
			if($categories)
				$children = $children->whereHas('type', function($q)
				{
				    $cat = Category::find(Input::get('category_id'))->getDescendantsAndSelf(array('id'));
				    $q->whereIn('category_id', array_flatten($cat->toArray()));

				});

            if($updated_at || $created_at)
            {
                if($updated_at) $children = $children->time('updated_at');
                else $children = $children->time('created_at');
            }

            $order = Input::get('order_by', null);
            $orderBy = $order ? explode(',', $order) : $order;
            if($orderBy) 
            {
            	/**
            	#TODO need Validations
            	**/
            	$children = $children->orderBy($orderBy[0], $orderBy[1]);
            }
            
            $children = $children->offset($offset)->limit($limit)->with('type', 'articles', 'gallery')->get();
            $children->each(function($child) use ($fields, $field){
            	if($field) $child->setVisible($fields);  
            });

            return Response::listing(
                array(
                    'code'=>200,
                    'message'=> 'success'
                ),
                $children, $offset, $limit
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
 			$child = Child::create(array(
 				'title' => Input::get('title'),
 				'content' => Input::get('content'),
 				'first_name' => Input::get('first_name'),
 				'last_name' => Input::get('last_name'),
 				'nickname' => Input::get('nickname'),
 				'gender' => Input::get('gender'),
 				'lost_age' => Input::get('lost_age'),
 				'place_of_missing' => Input::get('place_of_missing'),
 				'latitude' => Input::get('latitude'),
 				'longitude' => Input::get('longitude'),
 				'note' => Input::get('note'),
 				'order' => Input::get('order', 0),
 				'missing_date' => Input::get('missing_date'),
 				'report_date' => Input::get('report_date')
 			));

 			$picture = Input::get('picture', null);
			if($picture)
			{
				$response = Image::upload($picture);
				if(is_object($response)) return $response;
				$child->picture = $response;
			}

			$category_id = Input::get('category_id', null);
			if($category_id) 
			{
				$ids = explode(',', $category_id); 
				$child->attachRelations('type',$ids);
			}

			$gallery = Gallery::create(
                array(
                    'app_id' => Appl::getAppIDByKey(Input::get('appkey')),
                    'content_id' => $child->id,
                    'content_type' => 'child',
                    'name' => $child->first_name .'\'s gallery',             
                    'publish_at' => Input::get('publish_at', Carbon::now()->timestamp),
                )
            );

            if($gallery->save())
            	$child->gallery_id = $gallery->getKey();
            
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
			$field = Input::get('fields', null);
			$fields = explode(',', $field);

			$child = Child::active()->whereId($id)->with('type', 'articles.gallery.medias', 'gallery.medias')->first();

			if($field) $child->setVisible($fields);  

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
				$child->syncRelations('type', $ids);
				unset($inputs['category_id']);
			}

            $picture = Input::get('picture', null);
            if($picture)
            {
                $response = Image::upload($picture);
                if(is_object($response)) return $response;
                $inputs['picture'] = $response;
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
	        $child = Child::where('id','=',$id)->first();
	        if($child)
	        {
	        	$child->detachCategories(array_flatten($child->getCategoryIds()));	
				$child->delete();

				return Response::message(200, 'Deleted missingchild_'.$id.' success!'); 
	        }
			
			return Response::message(204, 'missingchild_id:'.$id.' does not exists!'); 
		}

		return Response::message(400, $validator->messages()->first());
	}

}