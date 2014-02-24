<?php namespace Kitti\Medias\Controllers;

use Appl;
use BaseController;
use Carbon\Carbon;
use Input;
use Image;
use Response;
use Validator;
use Kitti\Medias\Media;

class MediasController extends BaseController 
{
    public function index()
    {
        $validator = Validator::make(Input::all(), Media::$rules['show']);

        if($validator->passes())
        {
            $medias = Media::app()->apiFilter()->get();

            $medias->each(function($media){
                $media->fields();   
            });

            return Response::result(
                array(
                    'header'=> array(
                        'code'=> 200,
                        'message'=> 'success'
                    ),
                    'offset' => (int) Input::get('offset', 0),
                    'limit' => (int) Input::get('limit', 10),
                    'total' => Media::app()->count(),
                    'entries' => $medias->toArray()
                )
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
        $inputs = Input::all();
        $validator = Validator::make($inputs, Media::$rules['create']);

        if($validator->passes())
        {
            $media = Media::create(array(
                'app_id' => Appl::getAppIDByKey(Input::get('appkey')),
                'gallery_id' => Input::get('gallery_id'),
                'name' => Input::get('name', 'Image-'.Carbon::now()->toDateString()),
                'description' => Input::get('description'),
                'link' => Input::get('link'),
                'type' => Input::get('type'),
                'latitude' => Input::get('latitude'),
                'longitude' => Input::get('longitude')
            ));

            if(Input::get('picture', null))
            {
                $response = $media->createPicture($media->app_id);
                if(is_object($response)) return $response;              
                unset($inputs['picture']);
            }

            if($media->save())
                return Response::result(
                    array(
                        'header'=> array(
                            'code'=> 200,
                            'message'=> 'success'
                        ),
                        'id'=> $media->id,
                        'picture'=> $media->picture
                    )
                ); 
        }

        return Response::message(400, $validator->messages()->first());
    }

    public function show($id)
    {
        $validator = Validator::make(Input::all(), Media::$rules['show']);

        if($validator->passes())
        {
            $media = Media::app()->apiFilter()->find($id);

            return Response::result(
                array(
                    'header'=> array(
                        'code'=> 200,
                        'message'=> 'success'
                    ),
                    'entry'=> $media->toArray()
                )
            ); 
        }

        return Response::message(400, $validator->messages()->first());
    }

    public function edit($id)
    {
        return $this->update($id);
    }

    public function update($id)
    {   
        $inputs = array_add(Input::all(), 'id', $id);
        $validator = Validator::make($inputs, Media::$rules['update']);

        if($validator->passes())
        {
            $media = Media::app()->apiFilter()->find($id);

            foreach ($inputs as $key => $val) {
                if( $val == null || 
                    $val == '' || 
                    $val == $media[$key] ||
                    $key == 'appkey' ||
                    $key == 'id') 
                {
                    unset($inputs[$key]);
                }
            }

            if(!count($inputs))
                return Response::message(200, 'Nothing is update.');

            if(array_key_exists('picture', $inputs))
            {
                $response = $media->createPicture($media->app_id);
                if(is_object($response)) return $response;              
                unset($inputs['picture']);
            }

            if($media->update($inputs))
                return Response::message(200, 'Updated media id: '.$id.' success!');
        }

        return Response::message(400, $validator->messages()->first());
    }

    public function destroy($id)
    {
        $inputs = array_add(Input::all(), 'id', $id);
        $validator = Validator::make($inputs, Media::$rules['delete']);

        if($validator->passes())
        {
            $media = Media::app()->find($id);

            Image::delete($media->picture);
            
            if($media->delete())
            {
                return Response::message(200, 'Deleted media_id: '.$id.' success!'); 
            }

            return Response::message(204, 'media_id: '.$id.' does not exists!'); 
        }

        return Response::message(400, $validator->messages()->first());
    }
}