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
            $field = Input::get('fields', null);
            $fields = $field ? explode(',', $field) : $field;
            $article_missing = ArticleMissingchild::take($limit)->skip($offset)->with('article', 'missingchild')->get($fields);
            
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
            $rules = array(
                'gallery_id' => 'required',
                'category_id' => 'required',
                'missingchild_id' => 'required',
                'title'  => 'required',
                'content' => 'required',                
            );
            
            $validator = Validator::make(Input::all(), $rules);
            
            if ($validator->fails()) {
                $msg = $validator->messages()->first();
                return Response::json(array(
                        'header'=> array(
                            'code' => 204, 
                            'message' => $msg
                    )
                ));
            }else{
                date_default_timezone_set('Asia/Bangkok');
                
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
            $field = Input::get('fields', null);
            $fields = $field ? explode(',', $field) : '*';
            $article_missing = ArticleMissingchild::with('missingchild', 'article')->select($fields)->where('id', '=', $id)->get();
            
            if($article_missing)
                return Response::json(array(
                    'header'=> array(
                        'code'=>200, 
                        'message'=>'success'
                    ),
                    'entries'=> [$article_missing->toArray()]
            ));
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
            $rules = array(
//                'title' => 'required|exists:articles,title',
            );
            
            $validator = Validator::make(Input::all(), $rules);
            
            if ($validator->fails()) {
                $msg = $validator->messages()->first();
                return Response::json(array(
                        'header'=> array(
                            'code' => 204, 
                            'message' => $msg
                    )
                ));
            }
            
            $article_missing = ArticleMissingchild::with('missingchild', 'article')->find($id);
            return $article_missing; // **********************
            $app_id = Input::get('app_id', $article_missing->article->app_id);            
            $gallery_id = Input::get('gallery_id', $article_missing->gallery_id);
            $category_id = Input::get('category_id', $article_missing->category_id);
            $pre_title = Input::get('pre_title', $article_missing->pre_title);
            $title = Input::get('title', $article_missing->title);
            $picture = Input::get('picture', $article_missing->picture);
            $teaser = Input::get('teaser', $article_missing->teaser);
            $content = Input::get('content', $article_missing->content);
            $wrote_by = Input::get('wrote_by', $article_missing->wrote_by);
            $publish_date = Input::get('publish_date', $article_missing->publish_date);
            $views = Input::get('views', $article_missing->views);
            $tags = Input::get('tags', $article_missing->tags);
            $status = Input::get('status', $article_missing->status);
            
            date_default_timezone_set('Asia/Bangkok');
            
            if($article_missing){
                $result = $article_missing->where('id', '=', $id)->update(array(
                    'member_id' => $app_id,
                    'user_id' => $gallery_id,
                    'place_of_missing' => $category_id,
                    'place_of_report' => $pre_title,
                    'reporter' => $title,
                    'relationship' => $picture,
                    'note' => $teaser,
                    'approved' => $content,
                    'follow' => $wrote_by,
                    'founded' => $publish_date,
                    'public' => $views,
                    'order' => $tags,
                    'status' => $status,
                ));

                if($result)
                    return Response::json(array(
                        'header'=> array(
                            'code'=>200, 
                            'message'=>'success'
                        ),
                        'id'=> $mcs->id
                    ));
                else
                    return 'some error';
            } else {
                return Response::json(array(
                    'code'=>204, 
                    'message'=>'no member found to update'
                ));
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
		//
	}

}