<?php namespace Kitti\Medias\Controllers;

use Appl, Carbon\Carbon, Input, Response;
use Kitti\Medias\Repositories\MediaRepositoryInterface;

class ApiMediasController extends \BaseController 
{
    public function __construct(MediaRepositoryInterface $medias)
    {
        parent::__construct();
        $this->medias = $medias;
    }
    public function index()
    {
        if($this->medias->validate('show'))
        {
            return Response::result(array(
                'header' => array(
                    'code' => 200,
                    'message' => 'success'
                ),
                'offset' => Input::get('offset', 0),
                'limit' => Input::get('limit', 10),
                'total' => $this->medias->count(),
                'entries' => $this->medias->all()
            ));
        }

        return Response::message(400, $this->medias->errors());
    }

    public function create()
    {
       return $this->store();
    }

    public function store()
    {
        if($this->medias->validate('create'))
        {
            $media = $this->medias->create(array(
                'app_id'        => Appl::getAppIDByKey(Input::get('appkey')),
                'gallery_id'    => Input::get('gallery_id'),
                'name'          => Input::get('name', 'Image-'.Carbon::now()->toDateString()),
                'description'   => Input::get('description', null),
                'link'          => Input::get('link', null),
                'type'          => Input::get('type', 'image'),
                'latitude'      => Input::get('latitude'),
                'longitude'     => Input::get('latitude')
            ));

            $response = $media->createPicture($media->app_id);
            if($response instanceof \Symfony\Component\HttpFoundation\Response) return $response; 

            $media->picture = $response;

            if($media->save())
            {
                return Response::result(array(
                    'header'=> array(
                        'code'=> 200,
                        'message'=> 'success'
                    ),
                    'id'=> $media->id,
                    'picture'=> $response ? $media->getAttribute('picture') : Input::get('picture')
                ));
            }

            return Response::message(500, 'Something went wrong while trying to create media.');
        }

        return Response::message(400, $this->medias->errors());
    }

    public function show($id)
    {
        $input = array_add(Input::all(), 'id', $id);

        if($this->medias->validate('show', $input))
        {
            $media = $this->medias->find($id);
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

        return Response::message(400, $this->medias->errors());
    }

    public function edit($id)
    {
        return $this->update($id);
    }

    public function update($id)
    {
        $input = array_add(Input::all(), 'id', $id);
        if($this->medias->validate('update', $input))
        {
            $media = $this->medias->update($id, $input);
            
            if(!is_null($media))
            {   
                if($media->save())
                {
                    return Response::message(200, 'Updated media id: '.$id.' success!');
                }
            }

            return Response::message(500, 'Something went wrong while trying to update media.');
        }

        return Response::message(400, $this->medias->errors());
    }

    public function delete($id)
    {
        return $this->destroy($id);
    }

    public function destroy($id)
    {
        $input = array_add(Input::all(), 'id', $id);

        if($this->medias->validate('delete', $input))
        {
            if($this->medias->delete($id))
            {
                return Response::message(200, 'Deleted media_id: '.$id.' success!'); 
            }

            return Response::message(204, 'media_id: '.$id.' does not exists!'); 
        }

        return Response::message(400, $this->medias->errors());
    }
}