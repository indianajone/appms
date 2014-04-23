<?php namespace Max\Missingchild\Controllers;

use Validator, Input, Response, Hash, Appl, Image, Cache;
use Indianajone\Categories\Category;
use Max\Missingchild\Models\Missingchild as Child;
use Baum\Extensions\Eloquent\Collection;
// use Max\Missingchild\Collection;
use Kitti\Articles\Article;
use Kitti\Galleries\Gallery;
use Carbon\Carbon;


use Max\Missingchild\Repositories\MissingchildRepositoryInterface;


class ApiMissingchildController extends \BaseController 
{
	public function __construct(MissingChildRepositoryInterface $child)
	{
		parent::__construct();
		$this->child = $child;
	}

	public function index()
	{
		if($this->child->validate('show'))
		{
			return Response::result(array(
				'header' => array(
					'code' => 200,
					'message' => 'success'
				),
				'offset' => Input::get('offset', 0),
				'limit' => Input::get('limit', 10),
				'total' => $this->child->count(),
				'entries' => $this->child->all()
			));
		}

		return Response::message(400, $this->child->errors());
	}

	public function create()
	{
		return $this->store();
	}

	public function store()
	{
		if($this->child->validate('create'))
		{
			$app_id = Appl::getAppIDByKey(Input::get('appkey'));
			$input = array_add(Input::all(), 'app_id', $app_id);

			$result = $this->child->create($input);

			if($result) 
				return Response::result(array_merge(array(
					'header' => array(
						'code' => 200,
						'message' => 'success'
					)
				), $result));

			return Response::message(500, 'Something went wrong while trying to create Missingchild');
		}

		return Response::message(400, $this->child->errors());

	}

	public function show($id)
	{
		$inputs = array_add(Input::all(),'id',$id);
		$validator = Validator::make($inputs, Child::$rules['show_with_id']);

		if($validator->passes())
		{
			$child = Child::apiFilter()->with('categories', 'gallery.medias', 'app_content.gallery.medias', 'articles')->find($id);

			$child->fields();

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