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

            if(is_null($gallery)) 
                return Response::message(500, 'Something wrong when trying to create gallery.');

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
        $inputs = array_merge(array('id'=>$id), Input::all());
        $validator = Validator::make($inputs, Gallery::$rules['show_with_id']);

        if($validator->passes())
        {
            $gallery = Gallery::with(array(
                'medias' => function($query)
                {
                    $query->take(10); //->select(array('gallery_id','id', 'name','link', 'picture', 'like'));
                }))->apiFilter()->find($id);

            $gallery->fields();

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
        $validator = Validator::make(array('id'=>$id, 'type'=>$type, 'appkey' => Input::get('appkey')), Gallery::$rules['show_by_owner']);

        if($validator->passes())
        {

            $galleries = Gallery::with(array(
                'medias' => function($query)
                {
                    $query->take(10);
                })
            )->apiFilter()->owner($type, $id)->get();

            $galleries->each(function($gallery) {
                $gallery->fields();
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
        $gallery = Gallery::find($id);
        $inputs = array_merge(array('id'=>$id), Input::all());
        $validator = Validator::make($inputs, Gallery::$rules['show_with_id']);

        if($validator->passes())
        {
            if($gallery)
            {

                $medias = $gallery->medias()->apiFilter()->get();

                return Response::result(
                    array(
                        'header'=> array(
                            'code'=> 200,
                            'message'=> 'success'
                        ),
                        'offset' => (int) Input::get('offset', 0),
                        'limit' => (int) Input::get('limit', 10),
                        'total' => $gallery->medias->count(),
                        'entries' => $medias->toArray()
                    )
                );
            }

            else 
            {
                return Response::message(204, 'Can not find any medias in this gallery');
            }
        }

        return Response::message(400,$validator->messages()->first());

    }

    public function edit($id)
    {
        return $this->update($id);
    }

    public function update($id)
    {
        $inputs = array_add(Input::all(),
            'id', $id
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

            if(Input::get('picture', null))
            {
                $response = $gallery->createPicture($gallery->app_id);
                if(is_object($response)) return $response;              
                unset($inputs['picture']);
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
        $inputs = array_add(Input::all(), 'id', $id);
        $validator = Validator::make($inputs, Gallery::$rules['delete']);

        if($validator->passes())
        {
            
            $gallery = Gallery::find($id);
            $gallery->medias()->delete();
            $gallery->delete();

            return Response::message(200, 'Deleted Gallery: '.$id.' success!');
        }

        return Response::message(400, $validator->messages()->first()); 
    }

}