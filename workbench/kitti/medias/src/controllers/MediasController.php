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
        $offset = Input::get('offset', 0);
        $limit= Input::get('limit', 10);
        $field = Input::get('fields', null);
        $fields = explode(',', $field);
        $validator = Validator::make(Input::all(), Media::$rules['show']);

        if($validator->passes())
        {
            $medias = Media::app()->active()->offset($offset)->limit($limit)->get();

            $medias->each(function($media) use ($fields, $field){
                if($field) $media->setVisible($fields);   
            });

            return Response::listing(
                array(
                    'code'      => 200,
                    'message'   => 'success'
                ),
                $medias, $offset, $limit
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
        $validator = Validator::make(Input::all(), Media::$rules['create']);

        if($validator->passes())
        {
            $media = new Media;
            $media->app_id = Appl::getAppIDByKey(Input::get('appkey'));
            $media->gallery_id = Input::get('gallery_id');
            $media->name = Input::get('name', 'Image-'.Carbon::now()->toDateString());
            $media->description = Input::get('description');
            $media->link = Input::get('link');
            $media->type = Input::get('type');
            $media->latitude = Input::get('latitude');
            $media->longitude = Input::get('longitude');

            $picture = Input::get('picture', null);

            if($picture)
            {
                $response = Image::upload($picture);
                if(is_object($response)) return $response;
                $media->picture = $response;
            }

            if($media->save())
                return Response::result(
                    array(
                        'header'=> array(
                            'code'=> 200,
                            'message'=> 'success'
                        ),
                        'id'=> $media->id
                    )
                ); 
        }

        return Response::message(204, $validator->messages()->first());
    }

    public function show($id)
    {
        # code...
    }
}