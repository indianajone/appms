<?php

use Articles\Article\Models\Articles;
use Articles\Article\Models\Likes;
use Articles\Article\Models\Categories;

class ArticlesController extends BaseController {

    public function getIndex() {
        return 'index';
    }

    public function getFields() {
        return Response::fields('articles');
    }

//    public function createArticles() {
//        echo '<pre>';
//        print_r(Input::all());
//    }

    public function createArticles() {
        $input = Input::all();
        if (is_array($input)) {
            //config required field
            $data_require = array('appkey', 'user_id', 'title', 'content');
            $check = self::check_key($data_require, $input);

            if ($check == 'success') {

                $articles = new Articles();
                // Mandatory
                $articles->app_id = $input['appkey'];
                $articles->title = $input['title'];
                $articles->content = $input['content'];
                // Optional
                if (isset($input['gallery_id'])) {
                    $articles->gallery_id = $input['gallery_id'];
                }
                if (isset($input['pre_title'])) {
                    $articles->pre_title = $input['pre_title'];
                }
                if (isset($input['picture'])) {
                    $articles->picture = $input['picture'];
                }
                if (isset($input['teaser'])) {
                    $articles->teaser = $input['teaser'];
                }
                if (isset($input['wrote_by'])) {
                    $articles->wrote_by = $input['wrote_by'];
                }
                if (isset($input['tags'])) {
                    $articles->wrote_by = $input['tags'];
                }
                if (isset($input['categories'])) {
                    $articles->categories_id = $input['categories'];
                }

                $articles->create_date = date('Y-m-d H:i:s');
                //$articles->save();
                if ($articles->save()) {
                    $response['header']['code'] = 200;
                    $response['header']['message'] = 'success';
                    $response['id'] = $articles->id;
                    return $response;
                } else {
                    return 'Query have problems';
                }
            } else {
                return $check;
            }
        } else {
            return 'Format data must be array.';
        }
    }

    public function like($id) {
        $input = Input::all();
        if (isset($input['appkey']) && isset($input['member_id'])) {
            $likes = new Likes();
            $likes->app_id = $input['appkey'];
            $likes->member_id = $input['member_id'];
            $likes->type = 'article';
            if (isset($id)) {
                $likes->content_id = $id;
            }
            $likes->status = (isset($input['status'])) ? $input['status'] : 1;
            $likes->save();
            if ($likes->save()) {
                return $likes->like_id;
            } else {
                return 'Like fail.';
            }
        } else {
            return 'appkey and member_id are not exist.';
        }
    }

    public function unlike($id) {
        $input = Input::all();
        if (isset($input['appkey']) && isset($input['member_id'])) {
            $likes = new Likes();
            $likes->app_id = $input['appkey'];
            $likes->member_id = $input['member_id'];
            $likes->type = 'article';
            if (isset($id)) {
                $likes->content_id = $id;
            }
            $likes->status = (isset($input['status'])) ? $input['status'] : 1;
            $likes->save();
            if ($likes->save()) {
                return $likes->like_id;
            } else {
                return 'Like fail.';
            }
        } else {
            return 'appkey and member_id are not exist.';
        }
    }

    public function updateArticles() {
        $input = Input::all();
        if (is_array($input)) {
            //config required field
            $data_require = array('appkey');
            $check = self::check_key($data_require, $input);

            if ($check == 'success') {

                foreach ($input as $key => $data) {
                    if ($key == 'appkey') {
                        $new['article_id'] = $data;
                    } else if ($key == 'user_id') {
                        
                    } else if ($key == 'categories') {
                        $new['categories_id'] = $data;
                    } else {
                        $new[$key] = $data;
                    }
                }

                $articles = Articles::where('article_id', '=', $input['appkey'])->update($new);
                return $articles;
            } else {
                return $check;
            }
        } else {
            return 'Format data must be array.';
        }
    }

    public function deleteArticles($id) {
        if ($id) {
            $affectedRows = Articles::where('article_id', '=', $id)->delete();
            return $affectedRows;
        }
    }

    public function getList() {
        $offset = Input::get('offset', 0);
        $limit = Input::get('limit', 10);

        if (Input::has('appkey')) {
            $article = Articles::where('article_id', '=', Input::get('appkey'))->get()->toArray();
        } else {
            $article = Articles::skip($offset)->take($limit)->get()->toArray();
        }

        if ($article) {
            foreach ($article as $bigkey => $data) {
                foreach ($data as $key => $value) {
                    if ($key == 'categories_id') {
                        $categories = explode(",", $value);
                        $cat = Categories::getall($categories)->get()->toArray();
                        foreach ($cat as $key => $data) {
                            $result = Categories::where('category_id', '=', $data['category_id'])->with('children')->get()->toArray();
                            $article[$bigkey]['children'][] = $result;
                        }
                    }
                }
            }
        }

        return $article;
    }

    public function getFind() {
        $input = Input::all();
        if (isset($input['appkey'])) {
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
            
            $article = $query->get()->toArray();
        } else {
            return 'error';
        }
        
        if ($article) {
            foreach ($article as $bigkey => $data) {
                foreach ($data as $key => $value) {
                    if ($key == 'categories_id') {
                        $categories = explode(",", $value);
                        $cat = Categories::getall($categories)->get()->toArray();
                        foreach ($cat as $key => $data) {
                            $result = Categories::where('category_id', '=', $data['category_id'])->with('children')->get()->toArray();
                            $article[$bigkey]['children'][] = $result;
                        }
                    }
                }
            }
        }
        
        return $article;
    }

    public static function check_key($data_require, $data) {
        foreach ($data_require as $key) {
            if (!array_key_exists($key, $data)) {
                return 'Failure :: required ' . $key . ' field';
            }
        }
        return 'success';
    }

}