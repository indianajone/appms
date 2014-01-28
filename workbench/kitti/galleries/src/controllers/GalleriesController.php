<?php namespace Kitti\Galleries\Controllers;
use \BaseController;
use \Input;
use \Response;
use \Validator;
use \Image;
use \Kitti\Galleries\Galleries;
use \Kitti\Medias\Likes;
use \Kitti\Medias\Medias;
use \Kitti\Medias\Members;

class GalleriesController extends BaseController {
    private $media;

	public function fields() {
        if(Input::get('format') == 'xml') {
            return Response::fields('galleries','xml');
        } else {
            return Response::fields('galleries');
        }
    }

    public function create() {
        $input = Input::all();
        $validator = Validator::make(
            $input, array(
                'appkey' => 'required',
                'content_id' => 'required',
                'content_type' => 'required',
                'name' => 'required'
            )
        );

        $format = Input::get('format','json');
        
        if ($validator->passes()) {
            $galleries = new Galleries();
            // Mandatory
            // $galleries->app_id  = Appl::getAppID();
            $galleries->app_id = 1;
            $galleries->content_id = Input::get('content_id');
            $galleries->type = Input::get('content_type');
            $galleries->name = Input::get('name');

            // Optional
            $galleries->description = Input::get('gallery_id', null);
            $picture = Input::get('picture', null);

            /**
            #TODO: BASE64 Image test
            **/
            $getcontent = file_get_contents('http://3.bp.blogspot.com/-dFUF-DvQJP4/UZJ1uwO88EI/AAAAAAAAABE/y_YDbwLx7k4/s1600/%E0%B8%94%E0%B8%A7%E0%B8%87%E0%B8%94%E0%B8%B2%E0%B8%A7.jpg');
            $picture = base64_encode($getcontent);

            if($picture != null)
            {
                $response = Image::upload($picture);
                if(is_object($response)) return $response;
                $galleries->picture = $response;
            }

            $galleries->like = Input::get('like', 0);
            $galleries->publish_at = Input::get('publish_at', time());
            $galleries->status = Input::get('status', 1);
            
            if ($galleries->save()) {
                $response = array(
                	'header' => array(
                		'code' => '200',
                		'message' => 'success'),
                	'id'	=> $galleries->id
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
        $format = Input::get('format', 'json');
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
            $likes->type = 'gallery';
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
        $format = Input::get('format', 'json');
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
                'appkey' => 'required',
                'name' => 'required'
            )
        );

        $picture = Input::get('picture', null);
                    /**
        #TODO: BASE64 Image test
        **/
        $getcontent = file_get_contents('http://3.bp.blogspot.com/-dFUF-DvQJP4/UZJ1uwO88EI/AAAAAAAAABE/y_YDbwLx7k4/s1600/%E0%B8%94%E0%B8%A7%E0%B8%87%E0%B8%94%E0%B8%B2%E0%B8%A7.jpg');
        $picture = base64_encode($getcontent);

        if($picture)
        {
            $response = Image::upload($picture);
            if(is_object($response)) return $response;
            //$galleries->picture = $response;
            unset($input['picture']);
            $input['picture'] = $response;
        }
        
        if ($validator->passes()) {
            
            if(isset($input['format'])) { unset($input['format']);}
            if(isset($input['id'])) { unset($input['id']);}
            
            // $app_id = $input['appkey'];
            unset($input['appkey']);
            if(isset($input['content_type'])) {
                $input['type'] = $input['content_type'];
                unset($input['content_type']);
            }

            $galleries = Galleries::where('id', '=', $id)->update($input);
            
            $response = array();

            if ($galleries) {
                $response = array(
                	'header' => array(
                		'code' => '200',
                		'message' => 'success'
        			),
                	'id'	=> $id
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
            $articles = Galleries::where('id', '=', $id)->delete();
            $format = Input::get('format','json');
            if ($articles) {
                $response = array(
                	'header' => array(
                		'code' => '200',
                		'message' => 'success')
            	);
            	return Response::result($response, $format);
            }
        } 
    }

    public function lists() {
        $offset = Input::get('offset',0);
        $limit = Input::get('limit',10);
        $format = Input::get('format','json');

        $fields = array('id','app_id','content_id','type','name','description','picture','created_at','updated_at','publish_at');

        $galleries = Galleries::skip($offset)->take($limit)->get($fields)->toArray();
        $status['code'] = 200;
        $status['message'] = 'success';
        $galleries = self::getList($galleries);
        return Response::listing($status, $galleries, $offset , $limit , $format);
    }

    public function listId($id) {
        $offset = Input::get('offset',0);
        $limit = Input::get('limit',10);
        $format = Input::get('format','json');

        $fields = array('id','app_id','content_id','type','name','description','picture','created_at','updated_at','publish_at');

        $galleries = Galleries::where('id','=', $id)->get($fields)->toArray();
        // $status['code'] = 200;
        // $status['message'] = 'success';
        $galleries = self::getList($galleries , 'id' , $offset , $limit);
        $response = array(
            'header' => array(
                'code' => '200',
                'message' => 'success'
            ),
            'offset'    => $offset,
            'limit' => $limit,
            'total' => $this->media,
            'entries' => $galleries
        );

        return Response::result($response, $format);

        //return Response::listing($status, $galleries, $offset , $limit , $format);

    }

    private function getList($galleries, $mode = 'all' , $offset = 0 , $limit = 10) {
       // $g_id = 0;
        foreach($galleries as $key => $info) {

            // FIND LIKE
            $like = Likes::where('content_id','=',$info['id'])->where('likes.type','=','gallery')
                ->join('members', 'likes.member_id', '=', 'members.id')
                ->select('members.id', 'members.first_name', 'members.last_name','members.username')
                ->get();

            $total = $like->count();
            $galleries[$key]['like']['total'] = $like->count();
            $galleries[$key]['like']['members'] = $like->toArray();

            // FIND MEDIAS

            if($mode == 'all') {
                $media = Medias::where('gallery_id','=',$info['id'])
                    ->select('id','name','description','path','filename','type','latitude','longitude','like','created_at','updated_at')
                    ->get();
                } else {
                $media = Medias::where('gallery_id','=',$info['id'])->skip($offset)->take($limit)
                    ->select('id','name','description','path','filename','type','latitude','longitude','like','created_at','updated_at')
                    ->get();
                }

            $this->media = $media->count();
            if($media->count() > 0) {
                $galleries[$key]['medias'] = $media->toArray();
            }

            //Find Owner
            $owner = Members::where('app_id','=',$info['app_id'])
                    ->select('id','parent_id','first_name','last_name','gender','email','username','birthday','created_at','updated_at')
                    ->get();
            if($owner->count() > 0) {
                $galleries[$key]['owner'] = $owner->toArray();
            }
            //$g_id++;
        }

        return $galleries;

    }

    public function medias($id) {
        $offset = Input::get('offset',0);
        $limit = Input::get('limit',10);
        $format = Input::get('format','json');

        $media = Medias::where('gallery_id','=',$id)->skip($offset)->take($limit)
            ->select('id','name','description','path','filename','type','latitude','longitude','created_at','updated_at')
            ->get()->toArray();

        foreach($media as $key => $data) {

            $like = Likes::where('content_id','=',$data['id'])->where('likes.type','=','media')
            ->join('members', 'likes.member_id', '=', 'members.id')
            ->select('members.id', 'members.first_name', 'members.last_name','members.username')
            ->get();

            $media[$key]['like']['total'] = $like->count();
            $media[$key]['like']['members'] = $like->toArray();
        }

        $status['code'] = 200;
        $status['message'] = 'success';

        return Response::listing($status, $media, $offset , $limit , $format);
    }

    public function content($content_type,$content_id) {
        $offset = Input::get('offset',0);
        $limit = Input::get('limit',10);
        $format = Input::get('format','json');

        $fields = array('id','app_id','content_id','type','name','description','picture','created_at','updated_at','publish_at');

        $galleries = Galleries::where('content_id','=',$content_id)
        ->where('type','=',$content_type)->skip($offset)->take($limit)->get($fields)->toArray();

        $galleries = self::getList($galleries , 'id' , $offset , $limit);
        $response = array(
            'header' => array(
                'code' => '200',
                'message' => 'success'
            ),
            'offset'    => $offset,
            'limit' => $limit,
            'total' => $this->media,
            'entries' => $galleries
        );

        return Response::result($response, $format);
    }
}