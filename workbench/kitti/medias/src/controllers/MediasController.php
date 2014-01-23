<?php namespace Kitti\Medias\Controllers;
use \BaseController;
use \Input;
use \Response;
use \Validator;
use \Kitti\Medias\Medias;
use \Kitti\Medias\Likes;

class MediasController extends BaseController {
	public function fields() {
        if(Input::get('format') == 'xml') {
            return Response::fields('medias','xml');
        } else {
            return Response::fields('medias');
        }
    }

    public function create() {
        $input = Input::all();
        $validator = Validator::make(
            $input, array(
                'appkey' => 'required',
                'gallery_id' => 'required',
                'name' => 'required',
                'data' => 'required',
                'type' => 'required'
            )
        );

        $format = Input::get('format','json');
        
        if ($validator->passes()) {
            $medias = new Medias();
            // Mandatory
            // $medias->app_id  = Appl::getAppID();
            $medias->app_id = 1;
            $medias->member_id = Input::get('member_id', null);
            $medias->gallery_id = Input::get('gallery_id');
            $medias->name = Input::get('name');
            $medias->type = Input::get('type');
            #TODO UPLOAD MEDIA
            $medias->path = Input::get('data', 1);
            $medias->filename = Input::get('data', 1);
            // Optional

            $medias->description = Input::get('gallery_id', null);
            $medias->latitude = Input::get('latitude', null);
            $medias->longitude = Input::get('longitude', null);
            $medias->status = Input::get('status', 1);
            
            if ($medias->save()) {
                $response = array(
                	'header' => array(
                		'code' => '200',
                		'message' => 'success'),
                	'id'	=> $medias->id
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
            $likes->member_id = Input::get('member_id');
            $likes->type = 'media';
            $likes->content_id = $id;
            $likes->status = Input::get('status', 1);

            if ($likes->save()) {
                $response = array(
                	'header' => array(
                		'code' => '200',
                		'message' => 'success'),
                	'id'	=> $likes->id
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
                    ->where('type', '=' , 'media')
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
            unset($input['appkey']);

            #TODO make data to picture & input
            unset($input['data']);

            $medias = Medias::where('id', '=', $id)->where('name','=', $input['name'])->update($input);
            
            $response = array();

            if ($medias) {
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
            $medias = Medias::where('id', '=', $id)->delete();
            $format = Input::get('format','json');
            if ($medias) {
                $response = array(
                	'header' => array(
                		'code' => '200',
                		'message' => 'success')
            	);
            	return Response::result($response, $format);
            }
        } 
    }

    public function lists($id) {

        $input = Input::all();
        $format = Input::get('format','json');
        
        // // $filter = array('id','pre_title','title','picture','teaser','content','wrote_by','tags','created_at','updated_at','publish_at','categories_id');
        $fields = array('app_id','member_id','gallery_id','path','filename');
        $medias = Medias::where('id','=',$id)->get();

        $medias->each(function($medias) use ($fields){
	 			$medias->setHidden($fields);
 		});
 		$medias = $medias->toArray();
        // $galleires = $galleires->toArray();
        //return $medias;
        $response = array(
        	'header' => array(
        		'code' => '200',
        		'message' => 'success'),
        	'entry' => $medias
    	);
    	return Response::result($response, $format);
    }
}