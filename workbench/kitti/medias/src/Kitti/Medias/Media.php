<?php namespace Kitti\Medias;

use Image;

class Media extends \Eloquent
{
    protected $table = 'medias';
    protected $guarded = array('id');
    protected $hidden = array('app_id', 'gallery_id', 'deleted_at');
    protected $touches = array('gallery');

    protected $rules = array(
        'show' => array(
            'appkey' => 'required|exists:applications'
        ),
        'show_with_id' => array(
            'appkey' => 'required|exists:applications',
            'id' => 'required|exists:medias'
        ),
        'create' => array(
            'appkey' => 'required|exists:applications',
            'picture' => 'required',
            'type' => 'required|in:image,video,audio',
            'gallery_id' => 'required|exists:galleries,id'
        ),
        'update' => array(
            'appkey' => 'required|exists:applications',
            'id' => 'required|existsinapp:medias,id,Kitti\\Medias\\Media'
        ),
        'delete' => array(
            'appkey' => 'required|exists:applications',
            'id' => 'required|existsinapp:medias,id,Kitti\\Medias\\Media'
        )
    );

    use \BaseModel;

    public static function boot()
    {
        static::deleting(function($media){
            Image::delete($media->getOriginal('picture'));
        });

        parent::boot();
    }

    public function scopeSearch($query)
    {
        return $this->keywords(array('name'));
    }

    public function gallery()
    {
        return $this->belongsTo('Kitti\Galleries\Gallery');
    }
}