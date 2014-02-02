<?php namespace Kitti\Articles\Controllers;

use \BaseController;
use \Input;
use \Response;
use \Validator;
use \Kitti\Articles\Article;
use \Kitti\Articles\Like;
use \Indianajone\Categories\Category;
use Carbon\Carbon;

class ArticlesController extends BaseController {

	public function index()
    {
        $offset = Input::get('offset', 0);
        $limit= Input::get('limit', 10);
        $field = Input::get('fields', null);
        $fields = explode(',', $field);

        $articles = Article::active()->skip($offset)->take($limit)->get();

        $articles->each(function($articles) {
        	$cat_id = explode(',', $articles->categories_id);
        	$articles->categories = Category::categories($cat_id)->with('children')->get()->toArray();
        });

        $articles->each(function($gallery) use ($fields, $field){
            if($field) $articles->setVisible($fields);   
        });

        return Response::listing(
            array(
                'code'      => 200,
                'message'   => 'success'
            ),
            $articles, $offset, $limit
        );
    }

    public function create()
    {
    	return $this->store();
    	//return 'yes';
    }

    public function store()
    {
    	$validator = Validator::make(Input::all(), Article::$rules['create']);

        if($validator->passes())
        {
            $articles = Article::create(
                array(
                    // 'app_id' => Appl::getAppIDByKey(Input::get('appkey'));
                    'app_id' => 1, // for testing
                    'gallery_id' => Input::get('gallery_id'),
                    'pre_title' => Input::get('pre_title', null),
                    'title' => Input::get('title'),
                    'teaser' => Input::get('teaser', null),
                    'content' => Input::get('content'),
                    'wrote_by' => Input::get('wrote_by'),
                    'views' => Input::get('views', 0),
                    'publish_at' => Input::get('publish_at', Carbon::now()->timestamp),
                    'tags' => Input::get('tags', null),
                    'status' => Input::get('status',1),
                    'categories_id' => Input::get('categories', null),
                    'user_id' => Input::get('user_id')
                )
            );

            $picture = Input::get('picture', null);
            
            if($picture)
            {
                $response = Image::upload($picture);
                if(is_object($response)) return $response;
                $articles->update(array('picture' => $response));
            }

            if($articles)
                return Response::result(
                    array(
                        'header'=> array(
                            'code'=> 200,
                            'message'=> 'success'
                        ),
                        'id'=> $articles->id
                    )
                ); 
        }

        return Response::message(400,$validator->messages()->first());
    }

    public function show($id)
    {

    	$validator = Validator::make(array('id'=>$id), Article::$rules['show']);

        if($validator->passes())
        {
	        $field = Input::get('fields', null);
	        $fields = explode(',', $field);

	        $articles = Article::active()->get()->find($id);
	        $articles->categories = Category::categories(explode(',', $articles->categories_id))
	        						->with('children')->get()->toArray();

        	if($field) $articles->setVisible($fields); 

	        $response = array(
	        	'header' => array(
	        		'code' => 200,
	        		'message' => 'success'
	    		),
	    		'entry' => $articles->toArray()
			);
	        return Response::result($articles);
	    }
    }

    public function showFind() {
    		$offset = Input::get('offset', 0);
        	$limit= Input::get('limit', 10);
       	 	$field = Input::get('fields', null);
        	$fields = explode(',', $field);

			$query = Article::query();
            //$query->where('app_id', '=', Input::get('appkey'));

            if(Input::has('q')) $query->title(Input::get('q'));
            if(Input::has('categories')) $query->categories(Input::get('categories'));

            $others = Input::except('q','categories');
            if($others) {
            	$query->others($otherss);
            }

            $articles = $query->skip($offset)->take($limit)->get();

            $articles->each(function($articles) {
        		$cat_id = explode(',', $articles->categories_id);
        		$articles->categories = Category::categories($cat_id)->with('children')->get()->toArray();
	        });

	        $articles->each(function($gallery) use ($fields, $field){
	            if($field) $articles->setVisible($fields);   
	        });

            return $articles;
    }

    public function createLike($id) {
    		$validator = Validator::make(Input::all() , Like::$rules['like']);
    		if($validator->passes()) {

				$like = Like::create(
	                array(
	                    // 'app_id' => Appl::getAppIDByKey(Input::get('appkey'));
	                    'app_id' => 1, // for testing
	                    'member_id' => Input::get('member_id'),
	                    'content_id' => $id,
	                    'type' => 'article',
	                    'status' => Input::get('status', 1)
	                )
	            );

				if($like) {
	                return Response::result(
	                    array(
	                        'header'=> array(
	                            'code'=> 200,
	                            'message'=> 'success'
	                        ),
	                        'id'=> $like->id
	                    )
	                );
				}
    		}

    		return Response::message(400,$validator->messages()->first());
    }

    public function deleteLike($id) {
    		$validator = Validator::make(Input::all() , Like::$rules['like']);
    		if($validator->passes()) {
				if ($validator->passes()) {
					Like::deleteLike($id, Input::get('member_id') , 'article');;
					return Response::message(200, 'Deleted like_content_id_.$id.' success!'); 
				}
				return Response::message(400, $validator->messages()->first()); 
    		}
    }

    public function edit($id)
    {
        return $this->update($id);
    }

    public function update($id)
    {
		/**
			#TODO: Find a better place for resolver.
		**/
		Validator::resolver(function($translator, $data, $rules, $messages)
		{
		    return new \Indianajone\Validators\Rules\ExistsOrNull($translator, $data, $rules, $messages);
		});

		//$validator = Validator::make(Input::all(), Article::$rules['update']);

		//if ($validator->passes()) {
			$article = Article::find($id);
			$article->pre_title = Input::get('pre_title', $article->pre_title);
			$article->title = Input::get('title', $article->title);
			$article->teaser = Input::get('teaser', $article->teaser);
			$article->content = Input::get('content', $article->content);
			$article->wrote_by = Input::get('wrote_by', $article->wrote_by);
			$article->tags = Input::get('tags', $article->tags);
			$article->status = Input::get('tags', $article->status);
			$article->categories_id = Input::get('categories', $article->categories_id);
			//$parent_id = Input::get('parent_id', $cat->parent_id, null);

			$picture = Input::get('picture', null);
            
            if($picture)
            {
                $response = Image::upload($picture);
                if(is_object($response)) return $response;
                $article->update(array('picture' => $response));
            } else {
            	$article->picture = $article->picture;
            }

			if($article->save())
				return Response::message(200, 'Updated article_id: '.$id.' success!'); 
		//}

		return Response::message(400, $validator->messages()->first()); 
    }

    public function delete($id)
    {
        return $this->destroy($id);
    }

    public function destroy($id)
    {
    	$validator = Validator::make(array( 'id' => $id), Article::$rules['delete']);

        if ($validator->passes()) {
            Article::find($id)->delete();
            return Response::message(200, 'Deleted article'.$id.' success!'); 
        }

        return Response::message(400, $validator->messages()->first()); 
    }
}

