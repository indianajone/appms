<?php namespace Kitti\Galleries\Controllers;
use \BaseController;
use \Input;
use \Response;
use \Validator;
use \Kitti\Galleries\Galleries;

class GalleriesController extends BaseController {
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
            $galleries->picture = Input::get('picture', null);
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
        
        if ($validator->passes()) {
            
            if(isset($input['format'])) { unset($input['format']);}
            if(isset($input['id'])) { unset($input['id']);}
            
            // $app_id = $input['appkey'];
            // unset($input['appkey']);

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

        // $input = Input::all();
        // $offset = Input::get('offset', 0);
        // $limit = Input::get('limit', 10);
        // $format = Input::get('format','json');
        
        // // $filter = array('id','pre_title','title','picture','teaser','content','wrote_by','tags','created_at','updated_at','publish_at','categories_id');

        // $galleires = Galleries::skip($offset)->take($limit)->get($filter);
        // $galleires = $galleires->toArray();

    }
}