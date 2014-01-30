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
        
        $validator = Validator::make(Input::all(), Gallery::$rules['show']);

        if($validator->passes())
        {
            $galleries = Gallery::active()->app()->with('owner')->offset($offset)->limit($limit)->get();
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

        return Response::message(204, $validator->messages()->first());
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
                    'app_id' => Appl::getAppIDByKey(Input::get('appkey')),
                    'content_id' => Input::get('content_id'),
                    'content_type' => $inputs['content_type'],
                    'name' => Input::get('name'),
                    'description' => Input::get('description'),
                    'publish_at' => Input::get('publish_at', Carbon::now()->timestamp),
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
        $inputs = array_merge(array('id'=>$id), Input::all());
        $validator = Validator::make($inputs, Gallery::$rules['show_with_id']);

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

        $inputs = array_merge(array('id'=>$id), Input::all());
        $validator = Validator::make($inputs, Gallery::$rules['show_with_id']);

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
        $inputs = array_merge(
            array('id'=>$id), 
            Input::all()
        );

        $validator = Validator::make($inputs, Gallery::$rules['update']);

        if($validator->passes())
        {
            $gallery = Gallery::find($id);

            foreach ($inputs as $key => $val) {
                if( $val == null || 
                    $val == '' || 
                    $val == $gallery[$key] ||
                    $key == 'appkey' ||
                    $key == 'id') 
                {
                    unset($inputs[$key]);
                }
            }

            if(!count($inputs))
                return Response::message(200, 'Nothing is update.');

            $picture = Input::get('picture', null);
            if($picture)
            {
                $response = Image::upload($picture);
                if(is_object($response)) return $response;
                $inputs['picture'] = $response;
            }

            if($gallery->update($inputs))
                 return Response::message(200, 'Updated gallery id: '.$id.' success!');

            return Response::message(500, 'Something wrong when trying to update gallery.');
        }

        return Response::message(400,$validator->messages()->first());
    }

    public function delete($id)
    {
        return $this->destroy($id);
    }

    public function destroy($id)
    {
         $validator = Validator::make(array('id'=>$id), Gallery::$rules['delete']);

         if($validator->passes())
        {
            $gallery = Gallery::find($id)->delete();
            return Response::message(200, 'Deleted Gallery: '.$id.' success!');
        }

        return Response::message(400, $validator->messages()->first()); 
    }

}