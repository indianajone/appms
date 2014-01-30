<?php
namespace Max\Missingchild\Controllers;

use Validator, Input, Response, Appl;
use Max\Missingchild\Models\Article;
use Max\Missingchild\Models\ArticleMissingchild;
use Max\Application\Models\Application;

class ArticleMissingchildController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{            
            $offset = Input::get('offset', 0);
            $limit = Input::get('limit', 10);
            $field = Input::get('fields', null);
            $fields = $field ? explode(',', $field) : $field;
            
            $updated_at = Input::get('updated_at', null);
            $created_at = Input::get('created_at', null);
            
            $article_missing = ArticleMissingchild::with('article');
            
            if($updated_at || $created_at)
            {
                if($updated_at) $article_missing = $article_missing->time('updated_at');
                else $article_missing = $article_missing->time('created_at');
            }
            
            $article_missing = $article_missing->offset($offset)->limit($limit)->get();
            
            if($field)
                $article_missing->each(function($am) use ($fields){
                    $am->setVisible($fields);  
                });

                return Response::listing(
                    array(
                        'code'=>200,
                        'message'=> 'success'
                    ),
                    $article_missing, $offset, $limit
                );
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{            
            return $this->store();            
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
            $validator = Validator::make(Input::all(), ArticleMissingchild::$rules['create']);
            
            if ($validator->fails()) {
                return Response::message(204, $validator->messages()->first());
            }else{
                $article = new Article();
                $article->app_id = Appl::getAppIDByKey(Input::get('appkey'))->id;
                $article->gallery_id = Input::get('gallery_id');
                $article->category_id = Input::get('category_id');
                $article->pre_title = Input::get('pre_title');
                $article->title = Input::get('title');
                $article->picture = Input::get('picture');
                $article->teaser = Input::get('teaser');
                $article->content = Input::get('content');
                $article->wrote_by = Input::get('wrote_by');
                $article->publish_date = Input::get('publish_date');
                $article->views = Input::get('views');
                $article->tags = Input::get('tags');
                $article->status = Input::get('status', 0);

                if($article->save()){ 
                    $article_missings = new ArticleMissingchild();
                    $article_missings->article_id = $article->id;
                    $article_missings->missingchild_id = Input::get('missingchild_id');                
                    $article_missings->status = $article->status;
                    
                    if($article_missings->save()){     
                        return Response::result(array(
                            'header' => array(
                                'code'      => 200,
                                'message'   => 'success'
                            ),
                            'id' => $article_missings->id
                        ));
                    }else {
                        $article = Article::find($article->id)->delete();
                        
                        return Response::message(500, 'Something wrong when trying to save article missingchild.');
                    }   
                }else{    
                    return Response::message(500, 'Something wrong when trying to save article.');
                }             
            }
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
            $validator = Validator::make(array('id' => $id), ArticleMissingchild::$rules['show']);
            
            if ($validator->fails()) {
                return Response::message(204, $validator->messages()->first());
            }else{    
                $field = Input::get('fields');
                $fields = $field ? explode(',', $field) : '*';
                $article_missing = ArticleMissingchild::with('article')->select($fields)->where('id', '=', $id)->get();

                return Response::json(array(
                    'header'=> array(
                        'code'=>200, 
                        'message'=>'success'
                    ),
                    'entries'=> [$article_missing->toArray()]
                ));
            }
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{            
            return $this->update($id);            
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
            $validator = Validator::make(array('id' => $id), ArticleMissingchild::$rules['update']);
            
            if ($validator->fails()) {
                return Response::message(204, $validator->messages()->first());
            }else{
                $article_missing = ArticleMissingchild::find($id);
                
                foreach ($req as $key => $val) {
//                    if( $val == null || 
//                        $val == '' || 
//                        $val == $mcs[$key]) 
//                    {
//                        unset($req[$key]);
//                    }
//                }
//
//                if(!count($req))
//                    return Response::message(200, 'Nothing is update.');
                    
                }
                $input_am = array(
                    'article_id' => Input::get('article_id', $article_missing->article_id),          
                    'missingchild_id' => Input::get('missingchild_id', $article_missing->missingchild_id),
                    'status' => Input::get('status', $article_missing->status),
                );
                if($article_missing->where('id', '=', $id)->update($input_am)){              
                    $article = Article::find($article_missing->article_id);
                    
                    $input = array(
                        'app_id' => Input::get('app_id', $article->app_id),          
                        'gallery_id' => Input::get('gallery_id', $article->gallery_id),
                        'category_id' => Input::get('category_id', $article->category_id),
                        'pre_title' => Input::get('pre_title', $article->pre_title),
                        'title' => Input::get('title', $article->title),
                        'picture' => Input::get('picture', $article->picture),
                        'teaser' => Input::get('teaser', $article->teaser),
                        'content' => Input::get('content', $article->content),
                        'wrote_by' => Input::get('wrote_by', $article->wrote_by),
                        'publish_date' => Input::get('publish_date', $article->publish_date),
                        'views' => Input::get('views', $article->views),
                        'tags' => Input::get('tags', $article->tags),
                        'status' => Input::get('status', $article->status),
                    );
                    
                    if($article->where('id', '=', $article_missing->article_id)->update($input))
                        return Response::message(200, 'Updated article missingchild id: '.$id.' success!');
                    else
                        return Response::message(500, 'Something wrong when trying to update article missingchild.');      
                }else{
                    return Response::message(500, 'Something wrong when trying to update article.');
                }
            }
	}

        public function delete($id)
        {
            return $this->destroy($id);
        }
        
	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
            $validator = Validator::make(array('id' => $id), ArticleMissingchild::$rules['delete']);
            
            if ($validator->fails()) {
                return Response::message(400, $validator->messages()->first());     
            }else{      
                $article_missing = ArticleMissingchild::find($id);
                $article = Article::find($article_missing->article_id);
                
                if($article_missing->delete() && $article->delete()){
                    return Response::message(200, 'Deleted Article Missingchild id: '.$id.' success!');
                }else{
                    return Response::message(500, 'Something wrong when trying to delete article missingchild.'); 
                }
            }
	}

}