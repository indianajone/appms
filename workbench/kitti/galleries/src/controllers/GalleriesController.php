<?php namespace Kitti\Galleries\Controllers;
use \Appl;
use \BaseController;
use \Carbon\Carbon;
use \Input;
use \Response;
use \Validator;
use \Image;
use \Kitti\Galleries\Gallery;
use \Kitti\Medias\Media;
use \Kitti\Articles\Like;

class GalleriesController extends BaseController 
{
    public function index()
    {
        $offset = Input::get('offset', 0);
        $limit= Input::get('limit', 10);
        $field = Input::get('fields', null);
        $fields = explode(',', $field);

        $galleries = Gallery::active()->with('owner')->offset($offset)->limit($limit)->get();

        $galleries->each(function($galleries) {
            $like = Like::listByTypeMini($galleries->id ,'gallery')->get();
            $galleries->like = array( 'total' => $like->count(),'members' => $like->toArray());
        });

        $galleries->each(function($gallery) use ($fields, $field){
            if($field) $gallery->setVisible($fields);   
        });

        return Response::listing(
            array(
                'code'      => 200,
                'message'   => 'success'
            ),
            $galleries, $offset, $limit
        );
    }

    public function create()
    {
        return $this->store();
    }

    public function store()
    {
        $validator = Validator::make(Input::all(), Gallery::$rules['create']);

        if($validator->passes())
        {
            $gallery = Gallery::create(
                array(
                    // 'app_id' => Appl::getAppIDByKey(Input::get('appkey'));
                    'app_id' => 1, // for testing
                    'content_id' => Input::get('content_id'),
                    // 'content_type' => $inputs['content_type'],
                    'type' => Input::get('content_type'), // for testing
                    'name' => Input::get('name'),
                    'description' => Input::get('description'),
                    'publish_at' => Input::get('publish_at', Carbon::now()->timestamp),
                    'like' => Input::get('like',0), // for testing
                    'status' => Input::get('status',1), // for testing
                )
            );

            $response = FileUpload::upload(Input::get('data'));
            if(!is_array($response)) {
                $response = json_decode($response);
                if($response['code'] == 400) {
                    return $response;
                }
            }

            $picture = Input::get('picture', null);
            
            if($picture)
            {
                $response = Image::upload($picture);
                if(is_object($response)) return $response;
                $gallery->update(array('picture' => $response));
            }

            if($gallery)
                return Response::result(
                    array(
                        'header'=> array(
                            'code'=> 200,
                            'message'=> 'success'
                        ),
                        'id'=> $gallery->id
                    )
                ); 

        return Response::message(400,$validator->messages()->first()); 
        }
    }

    public function show($id)
    {
        $validator = Validator::make(array('id'=>$id), Gallery::$rules['show']);

        if($validator->passes())
        {
            $field = Input::get('fields', null);
            $fields = explode(',', $field);

            $gallery = Gallery::with(array('owner',
                'medias' => function($query)
                {
                    $query->take(10);
                }))->find($id)->get();

            $gallery->each(function($gallery) {
                $like = Like::listByTypeMini($gallery->id ,'gallery')->get();
                $gallery->like = array( 'total' => $like->count(),'members' => $like->toArray());
            });
            
            if($field) $gallery->setVisible($fields);  

            return Response::result(array(
                'header' => array(
                    'code' => 200,
                    'message' => 'success'
                ),
                'entry' => $gallery->toArray()
            ));
        }

        return Response::message(400,$validator->messages()->first());
    }

    public function showByOwner($type, $id)
    {
        $offset = Input::get('offset', 0);
        $limit= Input::get('limit', 10);
        $field = Input::get('fields', null);
        $fields = explode(',', $field);

        $galleries = Gallery::with(array(
            'medias' => function($query)
            {
                $query->take(10);
            })
        )->active()->owner($type, $id)->offset($offset)->limit($limit)->get();

        $galleries->each(function($galleries) {
            $like = Like::listByTypeMini($galleries->id ,'gallery')->get();
            $galleries->like = array( 'total' => $like->count(),'members' => $like->toArray());
        });

        $validator = Validator::make(array('id'=>$id, 'type'=>$type), Gallery::$rules['show_by_owner']);

        if($validator->passes())
        {
            $galleries->each(function($gallery) use ($fields, $field){
                $gallery->setHidden(array('content_id','type','app_id','status'));
                if($field) $gallery->setVisible($fields);   

            });

            return Response::listing(
                array(
                    'code'      => 200,
                    'message'   => 'success'
                ),
                $galleries, $offset, $limit
            );
        }

        return Response::message(400,$validator->messages()->first());
    }

    public function showMedias($id)
    {
        $offset = Input::get('offset', 0);
        $limit= Input::get('limit', 10);
        $field = Input::get('fields', null);
        $fields = explode(',', $field);

        $gallery = Gallery::find($id);

        $validator = Validator::make(array('id'=>$id), Gallery::$rules['show']);

        $galleries = $gallery->medias()->active()->offset($offset)->limit($limit)->get();

        $galleries->each(function($galleries) {
                $like = Like::listByTypeMini($galleries->id ,'gallery')->get();
                $galleries->like = array( 'total' => $like->count(),'members' => $like->toArray());
        });

        if($validator->passes())
        {
            return Response::listing(
                array(
                    'code'      => 200,
                    'message'   => 'success'
                ),
                $galleries->toArray(), $offset, $limit
            );
        }

        return Response::message(400,$validator->messages()->first());

    }

    public function showLike($id){
        $offset = Input::get('offset', 0);
        $limit= Input::get('limit', 10);
        $like = Like::listByType($id, 'gallery')->get();

        return Response::listing(
            array(
                'code'      => 200,
                'message'   => 'success'
            ),
            $like, $offset, $limit
        );
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
                        'type' => 'gallery',
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
                    Like::deleteLike($id, Input::get('member_id') , 'gallery');
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

        //$validator = Validator::make(Input::all(), Article::$rules['update']);

        //if ($validator->passes()) {
            $gallery = Gallery::find($id);
            $gallery->content_id = Input::get('content_id', $gallery->content_id);
            $gallery->type = Input::get('content_type', $gallery->type);
            $gallery->name = Input::get('name', $gallery->name);
            $gallery->description = Input::get('description', $gallery->description);

            $picture = Input::get('picture', null);
            
            if($picture)
            {
                $response = Image::upload($picture);
                if(is_object($response)) return $response;
                $gallery->update(array('picture' => $response));
            } else {
                $gallery->picture = $gallery->picture;
            }

            if($gallery->save())
                return Response::message(200, 'Updated gallery_id: '.$id.' success!'); 
        //}

        return Response::message(400, $validator->messages()->first()); 
    }

    public function delete($id)
    {
        return $this->destroy($id);
    }

    public function destroy($id)
    {
        $validator = Validator::make(array( 'id' => $id), Gallery::$rules['delete']);

        if ($validator->passes()) {
            Gallery::find($id)->delete();
            return Response::message(200, 'Deleted gallery_id : '.$id.' success!'); 
        }

        return Response::message(400, $validator->messages()->first()); 
    }
}