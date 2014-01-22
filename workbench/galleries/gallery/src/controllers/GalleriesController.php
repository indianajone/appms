<?php

use Galleries\Gallery\Models\Galleries;
use Galleries\Gallery\Models\Likes;
//use Galleries\Gallery\Models\Members;

class GalleriesController extends BaseController {

    //put your code here
    public function fields() {
        return Response::fields('articles');
    }

    public function lists() {
//        $offset = Input::get('offset', 0);
//        $limit = Input::get('limit', 10);
//
//        if (Input::has('appkey')) {
//            $galleries = Galleries::where('app_id', '=', Input::get('appkey'))->get()->toArray();
//        } else {
//            $galleries = Galleries::skip($offset)->take($limit)->get()->toArray();
//        }
//
//        if ($galleries) {
//            foreach ($galleries as $bigkey => $data) {
//                foreach ($data as $key => $value) {
//                    if ($key == 'categories_id') {
//                        $categories = explode(",", $value);
//                        $cat = Categories::getall($categories)->get()->toArray();
//                        foreach ($cat as $key => $data) {
//                            $result = Categories::where('category_id', '=', $data['category_id'])->with('children')->get()->toArray();
//                            $article[$bigkey]['children'][] = $result;
//                        }
//                    }
//                }
//            }
//        }
//
//        return $article;
    }
    
    public function getLike($id) {
        $offset = Input::get('offset', 0);
        $limit = Input::get('limit', 10);
        
        $input = Input::all();
        $validator = Validator::make(
            $input, array(
                'appkey' => 'required'
            )
        );
        
        if(!$validator->fails()) {
            $result = Likes::where('content_id','=',$id)
                    ->where('app_id','=',$input['appkey'])->with('entries')->get()->toArray();

            $response = array();
            $response['header'] = array(
                'code' => 200,
                'message' => 'success'
            );
            $response['limit'] = $limit;
            $response['offset'] = $offset;
            $response['total'] = count($result);
            foreach($result as $data) {
                if($data['entries'] != null) {
                    $response['entries'][] = $data['entries'];
                }
            }

            return $response;
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
            $likes->type = 'gallery';
            if (isset($id)) {
                $likes->content_id = $id;
            }
            $likes->status = (isset($input['status'])) ? $input['status'] : 1;
            if ($likes->save()) {
                return $likes->like_id;
            } else {
                return 'Like fail.';
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
        
        if(!$validator->fails()) {
            $result = Like::where('like_id','=',$id)
                    ->where('app_id','=',$input['appkey'])
                    ->where('member_id','=',$input['member_id'])
                    ->update(array('status' => 0));
            if($result) {
                
            } else {
                
            }
        } else {
            
        }
    }

    public function medias() {
        
    }

    public function postCreate() {
        $input = Input::all();
        $validator = Validator::make(
                        $input, array(
                    'appkey' => 'required',
                    'content_id' => 'required',
                    'content_type' => 'required|in:member,article',
                    'name' => 'required'
                        )
        );

        if (!$validator->fails()) {
            $galleries = new Galleries();
            $galleries->name = $input['name'];
            $galleries->content_id = $input['content_id'];
            $galleries->type = $input['content_type'];
            $galleries->app_id = $input['appkey'];

            if (isset($input['description'])) {
                $galleries->description = $input['description'];
            }
            if (isset($input['picture'])) {
                $galleries->picture = $input['picture'];
            }
            if (isset($input['like'])) {
                $galleries->like = $input['like'];
            }
            if (isset($input['status'])) {
                $galleries->status = $input['status'];
            } else {
                $galleries->status = 1;
            }
            if (isset($input['publish_date'])) {
                $galleries->publish_date = $input['publish_date'];
            }

            if ($galleries->save()) {
                return 'success';
            } else {
                return 'error';
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
                    'appkey' => 'required',
                    'name' => 'required',
                    'content_type' => 'in:member,article'
                        )
        );

        if (!$validator->fails()) {
            $input['app_id'] = $input['appkey'];
            unset($input['appkey']);
            if (isset($input['content_type'])) {
                $input['type'] = $input['content_type'];
                unset($input['content_type']);
            }

            $result = Galleries::where('gallery_id', '=', $id)
                    ->where('app_id', '=', $input['app_id'])
                    ->where('name', '=', $input['name'])
                    ->update($input);
            if ($result) {
                return 'success';
            } else {
                return 'error';
            }
        } else {
            $messages = $validator->messages();
            return $messages;
        }
    }

    public function delete($id) {
        if ($id) {
            $result = Galleries::where('gallery_id', '=', $id)->delete();
            return $result;
        }
    }

}
