<?php namespace Kitti\Galleries\Controllers;

use \Appl;
use \Carbon\Carbon;
use \Input;
use \Response;
use \Validator;
use \Image;
use \Kitti\Galleries\Gallery;
use \Kitti\Medias\Media;
use Kitti\Galleries\GalleryRepositoryInterface;

class ApiGalleriesController extends \BaseController 
{
    public function __construct(GalleryRepositoryInterface $galleries)
    {
        parent::__construct();
        $this->galleries = $galleries;
    }

    public function index()
    {      
        if($this->galleries->validate('show'))
        {
            return Response::result(array(
                'header' => array(
                    'code' => 200,
                    'message' => 'success'
                ),
                'offset' => Input::get('offset', 0),
                'limit' => Input::get('limit', 10),
                'total' => $this->galleries->count(),
                'entries' => $this->galleries->all()
            ));
        }

        return Response::message(400, $this->galleries->errors());
    }

    public function create()
    {
        return $this->store();
    }

    public function store()
    {
        if($this->galleries->validate('create'))
        {
            $owner = $this->galleries->owner(Input::get('content_type'));
            $gallery = $this->galleries->create(array(
                'app_id' => Appl::getAppIDByKey(Input::get('appkey')),
                'content_id' => Input::get('content_id'),
                'content_type' => get_class($owner->getModel()),
                'name' => Input::get('name'),
                'description' => Input::get('description'),
                'published_at' => Input::get('published_at', Carbon::now()->timestamp)
            ));

            $picture = Input::get('picture', null);

            if($picture)
            {
                $response = Image::upload($picture);
                if(is_object($response)) return $response;
                $gallery->picture = $response;
            }

            if($gallery->save())
                return Response::result(
                    array(
                        'header'=> array(
                            'code'=> 200,
                            'message'=> 'success'
                        ),
                        'id'=> $gallery->id
                    )
                );

            return Response::message(500, 'Something wrong when trying to create gallery.');
        }

        return Response::message(400, $this->galleries->errors());
    }

    public function show($id)
    {
        $input = array_add(Input::all(), 'id', $id);

        if($this->galleries->validate('show'))
        {
            return Response::result(array(
                'header' => array(
                    'code' => 200,
                    'message' => 'success'
                ),
                'entry' => $this->galleries->find($id)
                )
            );
        }

        return Response::message(400, $this->galleries->errors()); 
    }

    public function showMedias($id)
    {
        $input = array_add(Input::all(), 'id', $id);

        if($this->galleries->validate('show_with_id', $input))
        {
            $gallery = $this->galleries->find($id);
            $medias = $gallery->medias()->apiFilter()->get();

            return Response::result(array(
                'header' => array(
                    'code' => 200,
                    'message' => 'success'
                ),
                'offset' => (int) Input::get('offset', 0),
                'limit' => (int) Input::get('limit', 10),
                'total' => $gallery->medias->count(),
                'entries' => $medias->toArray()
            ));
        }

        return Response::message(400, $this->galleries->errors());
    }

    public function edit($id)
    {
        return $this->update($id);
    }

    public function update($id)
    {
        $inputs = array_add(Input::all(), 'id', $id);

        if($this->galleries->validate('update'))
        {
            $gallery = $this->galleries->update($id, Input::all());
            
            if(!is_null($gallery))
            {   
                if($gallery->save())
                    return Response::message(200, 'Updated gallery id: '.$id.' success!');
            }

            return Response::message(404, 'Selected application does not exists.');
        }

        return Response::message(400, $this->galleries->errors); 
    }

    public function delete($id)
    {
        return $this->destroy($id);
    }

    public function destroy($id)
    {
        $input = array_add(Input::all(), 'id', $id);

        if($this->galleries->validate('delete', $input))
        {
            if($this->galleries->delete($id))
            {
                 return Response::message(200, 'Deleted gallery_id: '.$id.' success!'); 
            }
        }

        return Response::message(400, $this->galleries->errors());
    }

}