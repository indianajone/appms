<?php
namespace Max\Missingchild\Controllers;

use Validator, Input, Response;
use Max\Missingchild\Models\Article, Max\Missingchild\Models\ArticleMissingchild, Max\Application\Models\Application;

class ArticleMissingchildController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
            $offset = Input::get('offset', 0);
            $limit= Input::get('limit', 10);
            $article_missing = ArticleMissingchild::take($limit)->skip($offset)->with('article', 'missingchild')->get();
            
            if($article_missing->count() > 0)
                return Response::listing(array(
                        'code'=>200, 
                        'message'=>'success'
                ), $article_missing, $offset, $limit);
            else 
                return Response::listing(array(
                        'code'=>204, 
                        'message'=>'no content'
                ), null, $offset, $limit);
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{            
            $validator = Validator::make(Input::all(), ArticleMissingchild::$rules['create']);
            
            if ($validator->fails()) {
                $msg = $validator->messages()->first();
                return Response::json(array(
                    'header'=> array(
                        'code' => 204, 
                        'message' => $msg
                    )
                ));
            }else{                
                $appkey = Input::get('appkey');
                $apps = Application::select('id')->where('appkey', '=', $appkey)->get();
                foreach ($apps as $app) {
                    $app_id = $app->id;
                }
                
                $article = new Article();
                $article->app_id = $app_id;
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
                        $arrResp['header']['code'] = 200;
                        $arrResp['header']['message'] = 'Success';
                        $arrResp['id'] = $article_missings->id;                
                        return Response::json($arrResp);                    
                    }else {
                        $article = Article::find($article->id)->delete();
                        
                        $arrResp['header']['code'] = 204;
                        $arrResp['header']['message'] = 'Cannot insert data';
                        return Response::json($arrResp);
                    }   
                } 
                else {    
                    $arrResp['header']['code'] = 204;
                    $arrResp['header']['message'] = 'Cannot insert data';
                    return Response::json($arrResp);
                }             
            }
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		//
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
                $msg = $validator->messages()->first();
                return Response::json(array(
                        'header'=> array(
                            'code' => 204, 
                            'message' => $msg
                    )
                ));
            }else{    
                $field = Input::get('fields');
                $fields = $field ? explode(',', $field) : '*';
                $article_missing = ArticleMissingchild::with('missingchild', 'article')->select($fields)->where('id', '=', $id)->get();

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
            $validator = Validator::make(array('id' => $id), ArticleMissingchild::$rules['update']);
            
            if ($validator->fails()) {
                $msg = $validator->messages()->first();
                return Response::json(array(
                    'header'=> array(
                        'code' => 204, 
                        'message' => $msg
                    )
                ));
            }else{
                $article_missing = ArticleMissingchild::find($id);
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
                        return Response::json(array(
                            'header'=> array(
                                'code'=>200, 
                                'message'=>'success'
                            ),
                            'id'=> $article_missing->id
                        ));
                    else
                        return Response::json(array(
                            'code'=>204, 
                            'message'=>'cannot update data'
                        ));       
                }else{
                    return Response::json(array(
                        'code'=>204, 
                        'message'=>'cannot update data'
                    ));  
                }
            }
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		//
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
                $msg = $validator->messages()->first();
                return Response::json(array(
                        'header'=> array(
                            'code' => 204, 
                            'message' => $msg
                    )
                ));
            }else{      
                $article_missing = ArticleMissingchild::find($id);
                $article = Article::find($article_missing->article_id);
                
                if($article_missing->delete() && $article->delete()){
                    return Response::json(array(
                        'header'=> array(
                            'code'=>200, 
                            'message'=>'success'
                        )
                    ));
                }else{
                    return Response::json(array(
                        'header'=> array(
                            'code'=>204, 
                            'message'=>'cannot delete'
                        )
                    ));
                }
            }
	}

}