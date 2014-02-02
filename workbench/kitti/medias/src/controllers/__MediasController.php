<?php namespace Kitti\Medias\Controllers;
use \BaseController;
use \Input;
use \Response;
use \Validator;
use \Kitti\Medias\Medias;
use \Kitti\Medias\Likes;
use \FileUpload;

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
            $medias->member_id = Input::get('member_id', 0);
            $medias->gallery_id = Input::get('gallery_id');
            $medias->name = Input::get('name');
            $medias->type = Input::get('type');

            /**
            #TODO UPLOAD MEDIA
            **/
            // $content = file_get_contents("http://localhost/dev/appms/public/filetest/9.mp4");
            // $base64 = base64_encode($content);
            // $response = FileUpload::upload($base64);
            // echo $response['path'];
            // echo $response['filename'];
            /**
            #TODO REMOVE COMMENT FOR REAL CODE BELOW
            **/
            $response = FileUpload::upload(Input::get('data'));
            if(!is_array($response)) {
                $response = json_decode($response);
                if($response['code'] == 400) {
                    return $response;
                }
            }

            $medias->path = $response['path'];
            $medias->filename = $response['filename'];
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
                    'id'        => $medias->id
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
<<<<<<< HEAD
                	'header' => array(
                		'code' => '200',
                		'message' => 'success'),
                	'id'	=> $likes->id
            	);
=======
                        'header' => array(
                                'code' => '200',
                                'message' => 'success'),
                        'id'        => $likes->id
                    );
>>>>>>> best
            }

        } else {
            $response = array(
<<<<<<< HEAD
            	'header' => array(
            		'code' => '400',
            		'message' => $validator->messages()->first()
    		));
=======
                    'header' => array(
                            'code' => '400',
                            'message' => $validator->messages()->first()
                    ));
>>>>>>> best
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
<<<<<<< HEAD
                	'header' => array(
                		'code' => '200',
                		'message' => 'success'
        		));
=======
                        'header' => array(
                                'code' => '200',
                                'message' => 'success'
                        ));
>>>>>>> best
            }
            
        } else {
            $response = array(
<<<<<<< HEAD
            	'header' => array(
            		'code' => '400',
            		'message' => $validator->messages()->first()
    		));
=======
                    'header' => array(
                            'code' => '400',
                            'message' => $validator->messages()->first()
                    ));
>>>>>>> best
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

            if (Input::has('data')) {
                $base64 = FileUpload::upload(Input::get('data'));
                /**
                #TODO UPLOAD MEDIA
                **/
                // $content = file_get_contents("http://localhost/dev/appms/public/filetest/4.gif");
                // $base64 = base64_encode($content);
                $response = FileUpload::upload($base64);
                /**
                #TODO REMOVE COMMENT FOR REAL CODE BELOW
                **/
                unset($input['data']);
                //print_r($response);
                $input['path'] = $response['path'];
                $input['filename'] = $response['filename'];
            }
<<<<<<< HEAD
            
=======

            // echo '<pre>';
            // print_r($input);
            // exit;

>>>>>>> best
            $medias = Medias::where('id', '=', $id)->where('name','=', $input['name'])->update($input);
            
            $response = array();

            if ($medias) {
                $response = array(
<<<<<<< HEAD
                	'header' => array(
                		'code' => '200',
                		'message' => 'success'
        			),
                	'id'	=> $id
            	);
=======
                        'header' => array(
                                'code' => '200',
                                'message' => 'success'
                                ),
                        'id'        => $id
                    );
>>>>>>> best
            } else {

            }
            
        } else {
            $response = array(
<<<<<<< HEAD
            	'header' => array(
            		'code' => '204',
            		'message' => $validator->messages()->first()
        		)
        	);
        }
    	return Response::result($response, $format);
=======
                    'header' => array(
                            'code' => '204',
                            'message' => $validator->messages()->first()
                        )
                );
        }
            return Response::result($response, $format);
>>>>>>> best
    }

    public function delete($id) {
        
        if ($id) {
            $medias = Medias::where('id', '=', $id)->delete();
            $format = Input::get('format','json');
            if ($medias) {
                $response = array(
<<<<<<< HEAD
                	'header' => array(
                		'code' => '200',
                		'message' => 'success')
            	);
            	return Response::result($response, $format);
=======
                        'header' => array(
                                'code' => '200',
                                'message' => 'success')
                    );
                    return Response::result($response, $format);
>>>>>>> best
            }
        } 
    }

    public function lists($id) {

        $input = Input::all();
        $format = Input::get('format','json');
        
        $fields = array('id','name','description','path','filename','type','latitude','longitude','created_at','updated_at');
        $medias = Medias::where('id','=',$id)->get($fields)->toArray();

        $like = Likes::where('content_id','=', $id)->where('likes.type','=','media')
        ->join('members', 'likes.member_id', '=', 'members.id')
        ->select('members.id', 'members.first_name', 'members.last_name','members.username')
        ->get();

        $medias[0]['like']['total'] = $like->count();
        $medias[0]['like']['members'] = $like->toArray();

        $response = array(
<<<<<<< HEAD
        	'header' => array(
        		'code' => '200',
        		'message' => 'success'),
        	'entry' => $medias
    	);
    	return Response::result($response, $format);
=======
                'header' => array(
                        'code' => '200',
                        'message' => 'success'),
                'entry' => $medias
            );
            return Response::result($response, $format);
>>>>>>> best
    }

    public function getLike($id) {
        $format = Input::get('format','json');
        $offset = Input::get('offset', 0);
        $limit = Input::get('limit', 10);
        $validator = Validator::make(
            Input::all(), array(
                'appkey' => 'required'
            )
        );

        if($validator->passes()) {
            $like = Likes::where('content_id','=',$id)->where('likes.type','=','media')
            ->join('members', 'likes.member_id', '=', 'members.id')
            ->select('members.id', 'members.parent_id','username' ,'first_name','last_name',
                'gender','email','phone','mobile','verified','fbid','fbtoken','birthday',
                'members.type','members.created_at','members.updated_at')
            ->skip($offset)->take($limit)
            ->get();

            $response = array(
            'header' => array(
                'code' => '200',
                'message' => 'success'),
            'offset' => $offset,
            'limit' => $limit,
            'total' => $like->count(),
            'entries' => $like->toArray()
            );
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
}