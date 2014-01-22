<?php

use Articles\Article\Models\Articles;
use Articles\Article\Models\Likes;
use Articles\Article\Models\Categories;

class ArticlesController extends BaseController {

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
        
        if (!$validator->fails()) {
            $articles = new Articles();
            // Mandatory
            $articles->app_id = $input['appkey'];
            $articles->title = $input['title'];
            $articles->content = $input['content'];
            $articles->user_id = $input['user_id'];
            // Optional
            if (isset($input['gallery_id'])){$articles->gallery_id = $input['gallery_id'];}
            if (isset($input['pre_title'])) {$articles->pre_title = $input['pre_title'];}
            if (isset($input['picture'])) {$articles->picture = $input['picture'];}
            if (isset($input['teaser'])) {$articles->teaser = $input['teaser'];}
            if (isset($input['wrote_by'])) {$articles->wrote_by = $input['wrote_by'];}
            if (isset($input['tags'])) {$articles->tags = $input['tags'];}
            if (isset($input['categories'])) {$articles->categories_id = $input['categories'];}
            if (isset($input['status'])) {$articles->status = $input['status'];} else { $articles->status = 1; }
            if (isset($input['publish_at'])) {$articles->publish_at = $input['publish_at'];} else { $articles->publish_at = date('Y-m-d H:i:s'); }
            
            if ($articles->save()) {
                $response['header']['code'] = 200;
                $response['header']['message'] = 'success';
                $response['id'] = $articles->article_id;
            } else {
                $response['header']['code'] = 400;
                $response['header']['message'] = 'failure : query have problem.';
            }
            
            if(isset($input['format']) && $input['format'] == 'xml') {
                return Response::xml($response);
            } else {
                return $response;
            }
        } else {
            $messages = $validator->messages();
            return $messages;
        }
    }

    public function like($id) {
        $input = Input::all();
        $validator = Validator::make(
            $input, array(
                'appkey' => 'required',
                'member_id' => 'required'
            )
        );
        
        if(!$validator->fails()) {
            $likes = new Likes();
            $likes->app_id = $input['appkey'];
            $likes->member_id = $input['member_id'];
            $likes->type = 'article';
            if (isset($id)) {
                $likes->content_id = $id;
            }
            $likes->status = (isset($input['status'])) ? $input['status'] : 1;
            if ($likes->save()) {
                $response['header']['code'] = 200;
                $response['header']['message'] = 'success';
                $response['id'] = $likes->like_id;
            } else {
                $response['header']['code'] = 400;
                $response['header']['message'] = 'failure : query have problem.';
            }
            
            if(isset($input['format']) && $input['format'] == 'xml') {
                return Response::xml($response);
            } else {
                return $response;
            }
        } else {
            $messages = $validator->messages();
            return $messages;
        }
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
        if(!$validator->fails()) {
            $result = Likes::where('content_id','=',$id)
                    ->where('app_id','=',$input['appkey'])
                    ->where('member_id','=',$input['member_id'])
                    ->update(array('status' => 0));
            if ($result) {
                $response['header']['code'] = 200;
                $response['header']['message'] = 'success';
                //return $response;
            } else {
                $response['header']['code'] = 400;
                $response['header']['message'] = 'failure : query have problem.';
                //return $response;
            }
            
            if(isset($input['format']) && $input['format'] == 'xml') {
                return Response::xml($response);
            } else {
                return $response;
            }
        } else {
            $messages = $validator->messages();
            return $messages;
        }
    }

    public function update($id) {
        $input = Input::all();
        $validator = Validator::make(
            $input, array(
                'appkey' => 'required'
            )
        );
        
        if (!$validator->fails()) {
            
            $format = Input::get('format','json');
            if(isset($input['format'])) { unset($input['format']);}
            if(isset($input['categories'])) {
                $input['categories_id'] = $input['categories'];
                unset($input['categories']);
            }
            
            $app_id = $input['appkey'];
            unset($input['appkey']);

            $articles = Articles::where('article_id', '=', $id)->where('app_id','=',$app_id)->update($input);
            
            $response = array();
            if($articles){
                $response['header']['code'] = 200;
                $response['header']['message'] = 'success';
                $response['id'] = $id;
                //return $response;
            } else {
                $response['header']['code'] = 400;
                $response['header']['message'] = 'failure : query have problem.';
                //return $response;
            }
            
            if($format == 'xml') {
                return Response::xml($response);
            } else {
                return $response;
            }
        } else {
            $messages = $validator->messages();
            return $messages;
        }
    }

    public function delete($id) {
        
        if ($id) {
            $affectedRows = Articles::where('article_id', '=', $id)->update(array('status' => 0));
            //return $affectedRows;
            if($affectedRows){
                $response['header']['code'] = 200;
                $response['header']['message'] = 'success';
                //return $response;
            } else {
                $response['header']['code'] = 400;
                $response['header']['message'] = 'failure : query have problem.';
                //return $response;
            }
            $format = Input::get('format','json');
            if($format == 'xml') {
                return Response::xml($response);
            } else {
                return $response;
            }
        }
    }

    public function lists() {
        $offset = Input::get('offset', 0);
        $limit = Input::get('limit', 10);
        
        $input = Input::all();
        $validator = Validator::make(
            $input, array(
                'appkey' => 'required'
            )
        );
        
        $filter = array('article_id','pre_title','title','picture','teaser','content','wrote_by','tags','created_at','updated_at','publish_at','categories_id');
        
        if(!$validator->fails()) {
            $article = Articles::where('app_id', '=', Input::get('appkey'))
                    ->skip($offset)->take($limit)->get($filter)->toArray();
        } else {
            $messages = $validator->messages();
            return $messages;
        }

        if ($article) {
            foreach ($article as $bigkey => $data) {
                foreach ($data as $key => $value) {
                    if ($key == 'categories_id') {
                        $categories = explode(",", $value);
                        $cat = Categories::getall($categories)->get()->toArray();
                        foreach ($cat as $key => $data) {
                            $result = Categories::where('category_id', '=', $data['category_id'])
                                    ->with('children')->get()->toArray();
                            $article[$bigkey]['categories'][] = $result;
                        }
                    }
                }
            }
            
            $format = Input::get('format','json');
            $status['code'] = 200;
            $status['message'] = 'success';
            return Response::listing($status,$article,$offset,$limit,$format);
        } else {
            $status['code'] = 400;
            $status['message'] = 'failure : query have problem.';
            return Response::message($status, $format);
        }

    }
    
    public function listsId($id) {

        $input = Input::all();
        $validator = Validator::make(
            $input, array(
                'appkey' => 'required'
            )
        );
        
        $filter = array('article_id','pre_title','title','picture','teaser','content','wrote_by','tags','created_at','updated_at','publish_at','categories_id');
        
        if(!$validator->fails()) {
            $article = Articles::where('app_id', '=', $input['appkey'])->where('article_id','=',$id)->get($filter)->toArray();

            if ($article) {
                foreach ($article as $bigkey => $data) {
                    foreach ($data as $key => $value) {
                        if ($key == 'categories_id') {
                            $categories = explode(",", $value);
                            $cat = Categories::getall($categories)->get()->toArray();
                            foreach ($cat as $key => $data) {
                                $result = Categories::where('category_id', '=', $data['category_id'])
                                        ->with('children')->get()->toArray();
                                $article[$bigkey]['categories'][] = $result;
                            }
                        }
                    }
                }
                
                $format = Input::get('format','json');
                $status['code'] = 200;
                $status['message'] = 'success';
                return Response::listing($status,$article,0,1,$format);
            } else {
                $status['code'] = 400;
                $status['message'] = 'failure : query have problem.';
                return Response::message($status, $format);
            }

        } else {
            $messages = $validator->messages();
            return $messages;
        }
    }

    public function getFind() {
        $offset = Input::get('offset', 0);
        $limit = Input::get('limit', 10);
        
        $input = Input::all();
        $validator = Validator::make(
            $input, array(
                'appkey' => 'required'
            )
        );
        
        $format = Input::get('format','json');
        
        if(!$validator->fails()) {
            $query = Articles::query();
            $query->where('app_id', '=', Input::get('appkey'));

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
            $filter = array('article_id','pre_title','title','picture','teaser','content','wrote_by','tags','created_at','updated_at','publish_at','categories_id');
            $article = $query->skip($offset)->take($limit)->get($filter)->toArray();

            if ($article) {
                foreach ($article as $bigkey => $data) {
                    foreach ($data as $key => $value) {
                        if ($key == 'categories_id') {
                            $categories = explode(",", $value);
                            $cat = Categories::getall($categories)->get()->toArray();
                            foreach ($cat as $key => $data) {
                                $result = Categories::where('category_id', '=', $data['category_id'])
                                        ->with('children')
                                        ->get()->toArray();
                                $article[$bigkey]['categories'][] = $result;
                            }
                        }
                    }
                }
                $status['code'] = 200;
                $status['message'] = 'success';
                return Response::listing($status,$article,$offset,$limit,$format);
            } else {
                $status['code'] = 400;
                $status['message'] = 'failure : query have problem.';
                return Response::message($status, $format);
            }

            return $article;
        } else {
            $status['code'] = 400;
            $status['message'] = $validator->messages();
            return Response::message($status, $format);
        }
    }
}