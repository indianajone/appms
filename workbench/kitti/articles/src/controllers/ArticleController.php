<?php namespace Kitti\Articles\Controllers;
use \BaseController;
use \Input;
use \Response;
use \Validator;
use \Kitti\Articles\Articles;
use \Indianajone\Categories\Category as Categories;

class ArticleController extends BaseController
{

	public function fields() {
        if(Input::get('format') == 'xml') {
            return Response::fields('articles','xml');
        } else {
            return Response::fields('articles');
        }
    }

    public function create() {
        $input = Input::all();
        $validator = Validator::make(
            $input, array(
                'appkey' => 'required',
                'user_id' => 'required',
                'title' => 'required',
                'content' => 'required'
            )
        );

        $format = Input::get('format','json');
        
        if ($validator->passes()) {
            $articles = new Articles();
            // Mandatory
            // $articles->app_id  = Appl::getAppID();
            $articles->app_id = 1;
            $articles->title = $input['title'];
            $articles->content = $input['content'];
            $articles->user_id = $input['user_id'];

            // Optional
            $articles->gallery_id = Input::get('gallery_id', null);
            $articles->pre_title = Input::get('pre_title', null);
            $articles->picture = Input::get('picture', null);
            $articles->teaser = Input::get('teaser', null);
            $articles->wrote_by = Input::get('wrote_by', null);
            $articles->tags = Input::get('tags', null);
            $articles->categories_id = Input::get('categories', null);
            $articles->status = Input::get('status', 1);
            $articles->publish_at = Input::get('publish_at', time());
            
            if ($articles->save()) {
                $response = array(
                	'header' => array(
                		'code' => '200',
                		'message' => 'success'),
                	'id'	=> $articles->id
            	);
            }
            
            
        } else {
            $response = array(
            	'header' => array(
            		'code' => '400',
            		'message' => $validator->messages()->first()
    		));
        }

        return Response::result($response, $format);
    }

    public function like($id) {
        //$input = Input::all();
        $validator = Validator::make(
            Input::all(), array(
                'appkey' => 'required',
                'member_id' => 'required'
            )
        );
        
        if($validator->passes()) {
            $likes = new Likes();
            // $likes->app_id  = Appl::getAppID();
            $likes->app_id = 1;
            $likes->member_id = Input::get('memeber_id');
            $likes->type = 'article';
            $likes->content_id = $id;
            $likes->status = Input::get('status', 1);

            if ($likes->save()) {
                $response = array(
                	'header' => array(
                		'code' => '200',
                		'message' => 'success'),
                	'id'	=> $likes->like_id
            	);
            }

        } else {
            $response = array(
            	'header' => array(
            		'code' => '400',
            		'message' => $validator->messages()->first()
    		));
        }

        return Response::result($response, $format);
    }

    public function unlike($id) {
        $input = Input::all();
        $validator = Validator::make(
            $input, array(
                'appkey' => 'required',
                'member_id' => 'required'
            )
        );
        
        $response = array();
        if($validator->passes()) {
            $result = Likes::where('id','=',$id)
                    ->where('member_id','=',$input['member_id'])
                    ->delete();
            if ($result) {
                 $response = array(
                	'header' => array(
                		'code' => '200',
                		'message' => 'success'
        		));
            }
            
        } else {
            $response = array(
            	'header' => array(
            		'code' => '400',
            		'message' => $validator->messages()->first()
    		));
        }
        return Response::result($response, $format);
    }

    public function update($id) {
        $input = Input::all();
        $format = Input::get('format','json');
        $validator = Validator::make(
            $input, array(
                'appkey' => 'required'
            )
        );
        
        if ($validator->passes()) {
            
            if(isset($input['format'])) { unset($input['format']);}
            if(isset($input['categories'])) {
                $input['categories_id'] = $input['categories'];
                unset($input['categories']);
            }
            
            $app_id = $input['appkey'];
            unset($input['appkey']);

            $articles = Articles::where('id', '=', $id)->update($input);
            
            $response = array();

            if ($articles) {
                $response = array(
                	'header' => array(
                		'code' => '200',
                		'message' => 'success')
            	);
            }
            
        } else {
            $response = array(
            	'header' => array(
            		'code' => '204',
            		'message' => $validator->messages()->first()
        		)
        	);
        }
    	return Response::result($response, $format);
    }

    public function delete($id) {
        
        if ($id) {
            $articles = Articles::where('id', '=', $id)->delete();
            if ($articles) {
                $response = array(
                	'header' => array(
                		'code' => '200',
                		'message' => 'success')
            	);
            	$format = Input::get('format','json');
            	return Response::result($response, $format);
            }
        } 
    }

    private static function getCategories($article , $offset = 0 , $limit = 10, $format = json) {
    	$field = array('id','name');

        if($article) {
            foreach ($article as $bigkey => $data) {
                foreach ($data as $key => $value) {
                    if ($key == 'categories_id') {
                        $categories = explode(",", $value);
                        $cat = Categories::getall($categories)->get()->toArray();

                        foreach ($cat as $key => $data) {
                            $result = Categories::where('id', '=', $data['id'])
                                    ->with('children')->get(array('id','name','parent_id'));
							$result = $result->toArray();

                            $article[$bigkey]['categories'][] = $result;
                        }
                    }
                }
            }
            $status['code'] = 200;
            $status['message'] = 'success';
            return Response::listing($status,$article,$offset,$limit,$format);
        } else {

        	$response = array(
            	'header' => array(
            		'code' => '204',
            		'message' => 'no results.')
        	);
        	return Response::result($response, $format);
        }
    }

    public function lists() {

        $input = Input::all();
        $offset = Input::get('offset', 0);
        $limit = Input::get('limit', 10);
        $format = Input::get('format','json');
        
        $filter = array('id','pre_title','title','picture','teaser','content','wrote_by','tags','created_at','updated_at','publish_at','categories_id');

        $article = Articles::skip($offset)->take($limit)->get($filter);
        $article = $article->toArray();

        return self::getCategories($article , $offset , $limit , $format);

    }
    
    public function listsId($id) {

        $input = Input::all();
        $offset = Input::get('offset', 0);
        $limit = Input::get('limit', 10);
        $format = Input::get('format','json');
        
        $filter = array('id','pre_title','title','picture','teaser','content','wrote_by','tags','created_at','updated_at','publish_at','categories_id');

        $article = Articles::where('id','=',$id)->get($filter);
        $field = array('id','name');

 		$article = $article->toArray();
 		return self::getCategories($article , $offset , $limit , $format);

    }

    public function find() {
        $offset = Input::get('offset', 0);
        $limit = Input::get('limit', 10);
        
        $input = Input::all();
        $validator = Validator::make(
            $input, array(
                'appkey' => 'required'
            )
        );
        
        $format = Input::get('format','json');
        
        if($validator->passes()) {
            $query = Articles::query();
            //$query->where('app_id', '=', Input::get('appkey'));

            if(isset($input['q'])) {
                $query->where('title', 'LIKE',"%".$input['q']."%");
            }

            if(isset($input['categories'])) {
                $cat_search = explode(",", $input['categories']);
                foreach($cat_search as $info) {
                    $query->where('categories_id','LIKE',"%".$info."%");
                }
            }

            unset($input['appkey']);
            unset($input['q']);
            unset($input['categories']);

            if(!empty($input)) {
                foreach($input as $key => $info) {
                    $query->where($key,'LIKE',"%".$info."%");
                }
            }


            $filter = array('id','pre_title','title','picture','teaser','content','wrote_by','tags','created_at','updated_at','publish_at','categories_id');

            $article = $query->skip($offset)->take($limit)->get($filter)->toArray();

 			return self::getCategories($article , $offset , $limit , $format);

        } else {
            $response = array(
            	'header' => array(
            		'code' => '204',
            		'message' => $validator->messages()->first()
        		)
        	);
        	return Response::result($response, $format);
        }
    }

}