<?php namespace Kitti\Medias\Controllers;

use \Appl;
use \BaseController;
use \Carbon\Carbon;
use \Input;
use \Response;
use \Validator;
use \Image;
use \FileUpload;
use \Kitti\Medias\Media;
use \Kitti\Articles\Like;

class MediasController extends BaseController
{
	public function index() {

		$offset = Input::get('offset', 0);
        $limit= Input::get('limit', 10);
        $field = Input::get('fields', null);
        $fields = explode(',', $field);

        $medias = Media::offset($offset)->limit($limit)->get();

        $medias->each(function($medias) {
            $like = Like::listByTypeMini($medias->id ,'media')->get();
            $medias->url = $medias->path.$medias->filename;
            unset($medias->path);
            unset($medias->filename);
            $medias->like = array( 'total' => $like->count(),'members' => $like->toArray());
        });

        $medias->each(function($medias) use ($fields, $field){
            if($field) $medias->setVisible($fields);   
        });

        return Response::listing(
            array(
                'code'      => 200,
                'message'   => 'success'
            ),
            $medias, $offset, $limit
        );
	}

	public function showLike($id){
        $offset = Input::get('offset', 0);
        $limit= Input::get('limit', 10);
        $like = Like::listByType($id, 'media')->get();

        return Response::listing(
            array(
                'code'      => 200,
                'message'   => 'success'
            ),
            $like, $offset, $limit
        );
    }

    public function create()
    {
        return $this->store();
    }

    public function store()
    {
        $validator = Validator::make(Input::all(), Media::$rules['create']);

        if($validator->passes())
        {
            $medias = Media::create(
                array(
                    // 'app_id' => Appl::getAppIDByKey(Input::get('appkey'));
                    'app_id' => 1, // for testing
                    'gallery_id' => Input::get('gallery_id'),
                    'name' => Input::get('name'), // for testing
                    // 'data' => Input::get('data'),
                    'type' => Input::get('type'),
                    'description' => Input::get('description' , null),
                    'latitude' => Input::get('latitude' , null),
                    'longitude' => Input::get('longitude' , null),
                    'like' => Input::get('like',0), // for testing
                    'status' => Input::get('status',1), // for testing
                )
            );

            /**
            #TODO UPLOAD MEDIA
            **/
            // $content = file_get_contents("http://localhost/dev/appms/public/filetest/9.mp4");
            // $data = base64_encode($content);
            // $file = FileUpload::upload($data);
            // echo $response['path'];
            // echo $response['filename'];
            // exit;
            /**
            #TODO REMOVE COMMENT FOR REAL CODE BELOW
            **/

            $file = FileUpload::upload(Input::get('data'));
            if(!is_array($file)) {
                $file = json_decode($file);
                if($file['code'] == 400) {
                    return $file;
                }
            }

            $medias->update(array('path' =>  $file['path'],'filename' => $file['filename']));

            if($medias)
                return Response::result(
                    array(
                        'header'=> array(
                            'code'=> 200,
                            'message'=> 'success'
                        ),
                        'id'=> $medias->id
                    )
                ); 
        }

        return Response::message(400,$validator->messages()->first());
    }

    public function show($id)
    {
        $validator = Validator::make(array('id'=>$id), Media::$rules['show']);

        if($validator->passes())
        {
            $field = Input::get('fields', null);
            $fields = explode(',', $field);

            $medias = Media::find($id)->get();

            $medias->each(function($medias) {
                $like = Like::listByTypeMini($medias->id ,'media')->get();
                $medias->url = $medias->path.$medias->filename;
	            unset($medias->path);
	            unset($medias->filename);
                $medias->like = array( 'total' => $like->count(),'members' => $like->toArray());
            });
            
            if($field) $medias->setVisible($fields);  

            return Response::result(array(
                'header' => array(
                    'code' => 200,
                    'message' => 'success'
                ),
                'entry' => $medias->toArray()
            ));
        }

        return Response::message(400,$validator->messages()->first());
    }

    public function createLike($id) {
        $validator = Validator::make(Input::all() , Like::$rules['like']);
            if($validator->passes()) {

                $like = Like::create(
                    array(
                        // 'app_id' => Appl::getAppIDByKey(Input::get('appkey'));
                        'app_id' => 1, // for testing
                        'member_id' => Input::get('member_id'),
                        'content_id' => $id,
                        'type' => 'media',
                        'status' => Input::get('status', 1)
                    )
                );

                if($like) {
                    return Response::result(
                        array(
                            'header'=> array(
                                'code'=> 200,
                                'message'=> 'success'
                            ),
                            'id'=> $like->id
                        )
                    );
                }
            }

            return Response::message(400,$validator->messages()->first());
    }

    public function deleteLike($id) {
            $validator = Validator::make(Input::all() , Like::$rules['like']);
            if($validator->passes()) {
                if ($validator->passes()) {
                    Like::deleteLike($id, Input::get('member_id') , 'media');
                    return Response::message(200, 'Deleted like_content_id_'.$id.' success!'); 
                }
                return Response::message(400, $validator->messages()->first()); 
            }
    }

    public function edit($id)
    {
        return $this->update($id);
    }

    public function update($id)
    {
        /**
        #TODO: Find a better place for resolver.
        **/
        Validator::resolver(function($translator, $data, $rules, $messages)
        {
            return new \Indianajone\Validators\Rules\ExistsOrNull($translator, $data, $rules, $messages);
        });

        $validator = Validator::make(Input::all(), Media::$rules['update']);

        if ($validator->passes()) {
            $medias = Media::find($id);
            $medias->gallery_id = Input::get('gallery_id', $medias->gallery_id);
            $medias->type = Input::get('type', $medias->type);
            $medias->name = Input::get('name', $medias->name);
            $medias->description = Input::get('description', $medias->description);
            $medias->latitude = Input::get('latitude', $medias->latitude);
            $medias->longitude = Input::get('longitude', $medias->longitude);
            $medias->status = Input::get('status', $medias->status);

            $media = Input::get('data', '');

            /**
            #TODO TEST UPLOAD MEDIA
            **/
            // $content = file_get_contents("http://localhost/dev/appms/public/filetest/6.3gp");
            // $media = base64_encode($content);
            
            if($media != '')
            {

            	$file = FileUpload::upload($media);

	            if(!is_array($file)) {
	                $file = json_decode($file);
	                if($file['code'] == 400) {
	                    return $file;
	                }
	            }

	            $medias->update(array('path' =>  $file['path'],'filename' => $file['filename']));
            } else {
                $medias->path = $medias->path;
                $medias->filename = $medias->filename;
            }

            if($medias->save())
                return Response::message(200, 'Updated media_id: '.$id.' success!'); 
        }

        return Response::message(400, $validator->messages()->first()); 
    }

    public function delete($id)
    {
        return $this->destroy($id);
    }

    public function destroy($id)
    {
        $validator = Validator::make(array( 'id' => $id), Media::$rules['delete']);

        if ($validator->passes()) {
            Media::find($id)->delete();
            return Response::message(200, 'Deleted media_id: '.$id.' success!'); 
        }

        return Response::message(400, $validator->messages()->first()); 
    }
}