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

class GalleriesController extends BaseController 
{
    public function index()
    {
        $offset = Input::get('offset', 0);
        $limit= Input::get('limit', 10);
        $field = Input::get('fields', null);
        $fields = explode(',', $field);

        $galleries = Gallery::active()->with('owner')->offset($offset)->limit($limit)->get();
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
        }

        return Response::message(400,$validator->messages()->first());
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
                }))->find($id);
            
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

        if($validator->passes())
        {
            return Response::listing(
                array(
                    'code'      => 200,
                    'message'   => 'success'
                ),
                $gallery->medias()->active()->offset($offset)->limit($limit)->get(), $offset, $limit
            );
        }

        return Response::message(400,$validator->messages()->first());

    }

    public function edit($id)
    {
        return $this->update($id);
    }

    public function update($id)
    {

    }

    public function delete($id)
    {
        return $this->destroy($id);
    }

    public function destroy($id)
    {

    }
}